<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ShippingController extends Controller
{
    public function index() {
        $customer = Auth::user()->customer;

        $orders = Order::with('orderItems.product')
            ->where('customer_id', $customer->id)
            ->where('status_payment', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Ship/Index', [
            'orders' => $orders
        ]);
    }
}
