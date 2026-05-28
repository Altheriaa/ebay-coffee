<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPendapatan = Order::where('status_payment', 'paid')->sum('total_price');
        $jumlahProduk = Product::count();
        $jumlahOrder = Order::count();
        $jumlahPelanggan = Customer::count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Total pendapatan dari order yang dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            
            Stat::make('Jumlah Produk', $jumlahProduk)
                ->description('Total produk aktif')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),
            
            Stat::make('Jumlah Order', $jumlahOrder)
                ->description('Total keseluruhan order')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Jumlah Pelanggan', $jumlahPelanggan)
                ->description('Total pelanggan terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
