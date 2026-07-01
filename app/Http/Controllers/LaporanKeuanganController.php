<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function print(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'owner'])) {
            abort(403);
        }

        $filterType = $request->query('filter_type', 'all');
        $selectedDate = $request->query('date', date('Y-m-d'));
        $selectedMonth = $request->query('month', date('m'));
        $selectedYear = $request->query('year', date('Y'));

        $orderQuery = Order::query()->where('status_payment', 'paid');

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
            $periodLabel = 'Hari Ini (' . Carbon::parse($selectedDate)->format('d M Y') . ')';
        } elseif ($filterType === 'bulan') {
            $periodLabel = 'Bulan ' . ($months[$selectedMonth] ?? '') . ' ' . $selectedYear;
        } elseif ($filterType === 'tahun') {
            $periodLabel = 'Tahun ' . $selectedYear;
        }

        return view('filament.pages.print', compact(
            'orders', 'totalPemasukan', 'periodLabel'
        ));
    }
}
