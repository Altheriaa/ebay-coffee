<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Transaction;

class PaymentNotificationController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function handle(Request $request)
    {
        try {
            $notif = new Notification;
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to parse Midtrans notification: '.$th->getMessage()], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $orderId = $notif->order_id;
        $fraud = $notif->fraud_status;

        // Verify signature key for security
        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$notif->status_code.$notif->gross_amount.$serverKey);

        if ($expectedSignature !== $notif->signature_key) {
            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        // Find the corresponding order by its invoice number
        $order = Order::with('orderItems.product')->where('invoice_number', $orderId)->first();

        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderStatus = 'pending';
        $paymentStatus = 'pending';

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $orderStatus = 'pending';
                    $paymentStatus = 'pending';
                } else {
                    $orderStatus = 'processing';
                    $paymentStatus = 'paid';
                }
            }
        } elseif ($transaction == 'settlement') {
            $orderStatus = 'processing';
            $paymentStatus = 'paid';
        } elseif ($transaction == 'pending') {
            $orderStatus = 'pending';
            $paymentStatus = 'pending';
        } elseif ($transaction == 'deny') {
            $orderStatus = 'cancelled';
            $paymentStatus = 'failed';
        } elseif ($transaction == 'expire') {
            $orderStatus = 'cancelled';
            $paymentStatus = 'expired';
        } elseif ($transaction == 'cancel') {
            $orderStatus = 'cancelled';
            $paymentStatus = 'failed';
        }

        DB::beginTransaction();
        try {
            // Deduct stock if payment transitions to PAID
            if ($paymentStatus === 'paid' && $order->status_payment !== 'paid') {
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->decrement('stok', $item->qty);
                    }
                }

                try {
                    $wa = new FonnteService;
                    $wa->notifyAdminNewOrder($order);
                    $wa->notifyOrderPaid($order);
                } catch (\Throwable $e) {
                    Log::warning('[Fonnte] Gagal kirim notifikasi pembayaran ke admin (handle): '.$e->getMessage());
                }
            }

            // Update order status
            $order->update([
                'status' => $orderStatus,
                'status_payment' => $paymentStatus,
                'payment_method' => $type,
            ]);

            // Save transaction details in payments table
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'transaction_id' => $notif->transaction_id,
                    'payment_type' => $type,
                    'transaction_status' => $transaction,
                    'payload' => json_encode($notif),
                ]
            );

            DB::commit();

            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(['message' => 'Database error: '.$th->getMessage()], 500);
        }
    }

    public function success(Request $request)
    {
        \Log::info('=== /payment/success HIT ===', ['payload' => $request->all()]);

        $request->validate([
            'order_id' => 'required|string',
        ]);

        $order = Order::with('orderItems.product')->where('invoice_number', $request->order_id)->first();

        if (! $order) {
            \Log::warning('Order not found for invoice: '.$request->order_id);

            return response()->json(['message' => 'Order not found', 'order_id' => $request->order_id], 404);
        }

        \Log::info('Order found', ['id' => $order->id, 'current_status_payment' => $order->status_payment]);

        if ($order->status_payment === 'paid') {
            \Log::info('Order already paid, skipping.');

            return response()->json(['message' => 'Already paid']);
        }

        $transactionStatus = null;
        $paymentType = null;
        $transactionId = null;
        $statusPayload = null;

        // First try: query Midtrans API for authoritative status
        try {
            $status = Transaction::status($request->order_id);

            Log::info('Midtrans API response', [
                'transaction_status' => $status->transaction_status ?? 'N/A',
                'payment_type' => $status->payment_type ?? 'N/A',
            ]);

            $transactionStatus = $status->transaction_status;
            $paymentType = $status->payment_type;
            $transactionId = $status->transaction_id ?? null;
            $statusPayload = json_encode($status);

        } catch (\Throwable $th) {
            Log::warning('Midtrans API status check failed: '.$th->getMessage());

            // Fallback: use values from Snap callback result sent by frontend
            $transactionStatus = $request->input('transaction_status');
            $paymentType = $request->input('payment_type');
            $transactionId = $request->input('transaction_id');
            $statusPayload = json_encode($request->all());

            \Log::info('Using Snap callback fallback', [
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
            ]);
        }

        if (! $transactionStatus) {
            Log::error('No transaction status available from API or callback.');

            return response()->json(['message' => 'Could not determine transaction status'], 422);
        }

        if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
            DB::beginTransaction();
            try {
                // Deduct stock if payment transitions to PAID
                foreach ($order->orderItems as $item) {
                    if ($item->product) {
                        $item->product->decrement('stok', $item->qty);
                    }
                }

                try {
                    $wa = new FonnteService;
                    $wa->notifyAdminNewOrder($order);
                    $wa->notifyOrderPaid($order);
                } catch (\Throwable $e) {
                    Log::warning('[Fonnte] Gagal kirim notifikasi (success): '.$e->getMessage());
                }

                $order->update([
                    'status' => 'processing',
                    'status_payment' => 'paid',
                    'payment_method' => $paymentType ?? 'midtrans',
                ]);

                // Save payment record
                Payment::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'transaction_id' => $transactionId,
                        'payment_type' => $paymentType,
                        'transaction_status' => $transactionStatus,
                        'payload' => $statusPayload,
                    ]
                );

                DB::commit();
                Log::info('Order updated to PAID successfully!');

                return response()->json(['message' => 'Payment synced successfully']);
            } catch (\Throwable $th) {
                DB::rollBack();
                Log::error('DB update failed: '.$th->getMessage());

                return response()->json(['message' => 'DB error: '.$th->getMessage()], 500);
            }
        } else {
            Log::info('Transaction not yet settled', ['status' => $transactionStatus]);

            return response()->json(['message' => 'Transaction status: '.$transactionStatus]);
        }
    }
}
