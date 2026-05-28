<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiwayatTransaksiController;
use App\Http\Controllers\PaymentNotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\LaporanKeuanganController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route Login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

// // forgot password
// Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
// Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');

// // reset password
// Route::get('/reset-password/{token}', [ResetPasswordController::class, 'index'])->name('password.reset');
// Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/', [HomeController::class, 'index']);

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
    Route::get('/baycoffee-admin/laporan-keuangan/print', [LaporanKeuanganController::class, 'print'])
        ->name('admin.laporan-keuangan.print');
});
