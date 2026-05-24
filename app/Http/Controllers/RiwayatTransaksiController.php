<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RiwayatTransaksiController extends Controller
{
    public function index() {
        $customer = Auth::user()->customer;

        $orders = Order::with('orderItems.product')->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Transaction/Index', [
            'orders' => $orders
        ]);
    }

    public function cancel(Order $order) {
        $customer = Auth::user()->customer;
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        if ($order->status_payment === 'pending') {
            $order->update([
                'status' => 'cancelled',
                'status_payment' => 'failed'
            ]);
            return back()->with('success', 'Transaksi berhasil dibatalkan.');
        }

        return back()->with('error', 'Transaksi tidak dapat dibatalkan.');
    }

    public function reorder(Order $order) {
        $customer = Auth::user()->customer;
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        foreach ($order->orderItems as $item) {
            $cartItem = \App\Models\CartItem::where('customer_id', $customer->id)
                ->where('product_id', $item->product_id)
                ->first();

            if ($cartItem) {
                $cartItem->update([
                    'qty' => $cartItem->qty + $item->qty
                ]);
            } else {
                \App\Models\CartItem::create([
                    'customer_id' => $customer->id,
                    'product_id' => $item->product_id,
                    'qty' => $item->qty
                ]);
            }
        }

        return redirect('/cart')->with('success', 'Semua item berhasil ditambahkan ke keranjang.');
    }

    public function invoice(Order $order) {
        $customer = Auth::user()->customer;
        if ($order->customer_id !== $customer->id) {
            abort(403);
        }

        $order->load(['orderItems.product', 'customer']);

        return Inertia::render('Transaction/Invoice', [
            'order' => $order
        ]);
    }
}
