<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/', function () {
    return Inertia::render('Home', [
        'message' => 'Selamat datang!'
    ]);
});

Route::get('/shop', [ProductController::class, 'index'])->name('shop');
Route::get('/shop/{product}', [ProductController::class, 'show'])->name('shop.show');

// Midtrans Webhook Notification
Route::post('/payment/notification', [PaymentNotificationController::class, 'handle']);
Route::post('/payment/success', [PaymentNotificationController::class, 'success']);

Route::middleware('auth')->group(function () {
    // keranjang
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::patch('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);

    // Checkout
    Route::post('/checkout', [CartController::class, 'checkout']);

    // Riwayat Transaksi
    Route::get('/transaksi', [RiwayatTransaksiController::class, 'index']);
    Route::post('/transaksi/{order}/cancel', [RiwayatTransaksiController::class, 'cancel']);
    Route::post('/transaksi/{order}/reorder', [RiwayatTransaksiController::class, 'reorder']);
    Route::get('/transaksi/{order}/invoice', [RiwayatTransaksiController::class, 'invoice']);

    // Ship Status
    Route::get('/shipping', [ShippingController::class, 'index']);

    // profile
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/password', [ProfileController::class, 'password']);

    // Print Laporan Keuangan Admin
    Route::get('/baycoffee-admin/laporan-keuangan/print', function (\Illuminate\Http\Request $request) {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $filterType = $request->query('filter_type', 'all');
        $selectedDate = $request->query('date', date('Y-m-d'));
        $selectedMonth = $request->query('month', date('m'));
        $selectedYear = $request->query('year', date('Y'));

        $orderQuery = \App\Models\Order::query()->where('status_payment', 'paid');

        if ($filterType === 'hari') {
            $orderQuery->whereDate('created_at', $selectedDate);
        } elseif ($filterType === 'bulan') {
            $orderQuery->whereMonth('created_at', $selectedMonth)->whereYear('created_at', $selectedYear);
        } elseif ($filterType === 'tahun') {
            $orderQuery->whereYear('created_at', $selectedYear);
        }

        $orders = $orderQuery->with('customer')->orderBy('created_at', 'desc')->get();

        $totalPemasukan = $orders->sum('total_price');

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $periodLabel = 'Semua Periode';
        if ($filterType === 'hari') {
            $periodLabel = 'Hari Ini (' . \Carbon\Carbon::parse($selectedDate)->format('d M Y') . ')';
        } elseif ($filterType === 'bulan') {
            $periodLabel = 'Bulan ' . ($months[$selectedMonth] ?? '') . ' ' . $selectedYear;
        } elseif ($filterType === 'tahun') {
            $periodLabel = 'Tahun ' . $selectedYear;
        }

        return view('filament.pages.print', compact(
            'orders', 'totalPemasukan', 'periodLabel'
        ));
    })->name('admin.laporan-keuangan.print');
});
