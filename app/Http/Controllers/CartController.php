<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index() {

        $customer = Auth::user()->customer;

        $carts = CartItem::with('product')
                ->where('customer_id', $customer->id)
                ->latest()
                ->get();

        return Inertia::render('Cart/Index', [
            'carts' => $carts,  
        ]);
    }

    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $customer = Auth::user()->customer;
        $product = Product::findOrFail($request->product_id);

        if ($request->qty > $product->stok) {

            return back()->with('error', 'Stock tidak cukup');
        }

        $cartItem = CartItem::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {

            $newQty = $cartItem->qty + $request->qty;
            // validasi stok
            if ($newQty > $product->stok) {

                return back()->with('error', 'Stock tidak cukup');
            }

            $cartItem->update([
                'qty' => $newQty,
            ]);
        } else {
            CartItem::create([
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'qty' => $request->qty
            ]);
        }
        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang');
    }

    public function update(Request $request, CartItem $cartItem) {
        $customer = Auth::user()->customer;
        if ($cartItem->customer_id !== $customer->id) {
            abort(403);
        }

        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        if ($request->qty > $cartItem->product->stok) {
            return back()->with('error', 'Stock tidak cukup');
        }

        $cartItem->update([
            'qty' => $request->qty,
        ]);

        return back()->with('success', 'Keranjang berhasil diperbarui');
    }

    public function destroy(CartItem $cartItem) {
        $customer = Auth::user()->customer;
        if ($cartItem->customer_id !== $customer->id) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produk berhasil dihapus dari keranjang');
    }

    public function checkout() {

        $customer = Auth::user()->customer;
        
        $carts = CartItem::with('product')
                ->where('customer_id', $customer->id)
                ->get();

        if ($carts->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        foreach ($carts as $item) {
            if ($item->qty > $item->product->stok) {
                return response()->json(['message' => "{$item->product->nama_product} stok tidak cukup"], 400);
            }
        }

        // create transaksi
        DB::beginTransaction();

        try {

            $subtotal = $carts->sum(function ($item) {
                return $item->qty * $item->product->harga;
            });

            $shippingCost = $subtotal >= 400000 ? 0 : 50000;

            $totalPrice = $subtotal + $shippingCost;

            $order = Order::create([
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'subtotal' => $subtotal,
                'total_price' => $totalPrice,
                'phone' => $customer->no_hp,
                'shipping_address' => $customer->alamat,
                'status' => 'pending',
                'status_payment' => 'pending'
            ]);

            foreach ($carts as $item) {

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'price' => $item->product->harga,
                    'qty' => $item->qty,
                    'subtotal' => $item->qty * $item->product->harga
                ]);
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->invoice_number,
                    'gross_amount' => $totalPrice,
                ],

                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'phone' => $customer->no_hp,
                ]
            ];

            $snapToken = Snap::getSnapToken($params);

            $order->update([
                'snap_token' => $snapToken
            ]);

            CartItem::where('customer_id', $customer->id)
                ->delete();

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();
            
            return response()->json(['message' => 'Gagal checkout: ' . $th->getMessage()], 500);
        }

    }
        
    
}
