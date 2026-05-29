<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Current month revenue
        $currentRevenue = Order::where('status_payment', 'paid')
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->sum('total_price');

        // Last month revenue
        $lastRevenue = Order::where('status_payment', 'paid')
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_price');

        // Revenue trend description
        $revenueDiff = $lastRevenue > 0
            ? round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1)
            : ($currentRevenue > 0 ? 100 : 0);
        $revenueDesc = $revenueDiff >= 0 ? "{$revenueDiff}% naik dari bulan lalu" : abs($revenueDiff) . '% turun dari bulan lalu';
        $revenueIcon = $revenueDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $revenueColor = $revenueDiff >= 0 ? 'success' : 'danger';

        // 7-day revenue chart data
        $revenueChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $revenueChart[] = Order::where('status_payment', 'paid')
                ->whereDate('created_at', $day)
                ->sum('total_price');
        }

        // Current month orders
        $currentOrders = Order::whereBetween('created_at', [$startOfMonth, $now])->count();
        $lastOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $orderDiff = $lastOrders > 0
            ? round((($currentOrders - $lastOrders) / $lastOrders) * 100, 1)
            : ($currentOrders > 0 ? 100 : 0);

        // 7-day order chart data
        $orderChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $orderChart[] = Order::whereDate('created_at', $day)->count();
        }

        // Customers
        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::whereBetween('created_at', [$startOfMonth, $now])->count();

        // 7-day new customer chart
        $customerChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $customerChart[] = Customer::whereDate('created_at', $day)->count();
        }

        // Pending orders
        $pendingOrders = Order::where('status', 'pending')
            ->where('status_payment', 'paid')
            ->count();
        $processingOrders = Order::where('status', 'processing')->count();

        // Average order value
        $avgOrder = Order::where('status_payment', 'paid')
            ->whereBetween('created_at', [$startOfMonth, $now])
            ->avg('total_price') ?? 0;

        return [
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($currentRevenue, 0, ',', '.'))
                ->description($revenueDesc)
                ->descriptionIcon($revenueIcon)
                ->color($revenueColor)
                ->chart($revenueChart),

            Stat::make('Total Order Bulan Ini', $currentOrders)
                ->description(($orderDiff >= 0 ? '+' : '') . $orderDiff . '% vs bulan lalu')
                ->descriptionIcon($orderDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($orderDiff >= 0 ? 'success' : 'danger')
                ->chart($orderChart),

            Stat::make('Total Pelanggan', $totalCustomers)
                ->description("+{$newCustomersThisMonth} pelanggan baru bulan ini")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart($customerChart),

            Stat::make('Rata-rata Order', 'Rp ' . number_format($avgOrder, 0, ',', '.'))
                ->description('Nilai rata-rata per transaksi')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('warning'),

            Stat::make('Perlu Diproses', $pendingOrders + $processingOrders)
                ->description("{$pendingOrders} pending · {$processingOrders} processing")
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders + $processingOrders > 0 ? 'warning' : 'success'),

            Stat::make('Total Produk', Product::count())
                ->description(Product::where('stok', '<=', 5)->count() . ' produk stok rendah')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color(Product::where('stok', '<=', 5)->count() > 0 ? 'danger' : 'success'),
        ];
    }
}
