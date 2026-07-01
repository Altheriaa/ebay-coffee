<?php

namespace App\Filament\Pages;

use App\Models\Order;
use BackedEnum;
use UnitEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LaporanKeuangan extends Page
{
    /**
     * Admin dan Owner bisa mengakses halaman Laporan Keuangan.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'owner']);
    }
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static string | UnitEnum | null $navigationGroup = 'Manajemen Keuangan';

    protected static ?string $navigationLabel = 'Laporan Keuangan';

    protected static ?string $title = 'Laporan Keuangan';

    protected string $view = 'filament.pages.laporan-keuangan';

    public string $filterType = 'bulan';
    public string $selectedDate = '';
    public string $selectedMonth = '';
    public string $selectedYear = '';

    public array $months = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ];

    public array $years = [];

    public function mount(): void
    {
        $this->filterType = 'bulan';
        $this->selectedDate = date('Y-m-d');
        $this->selectedMonth = date('m');
        $this->selectedYear = date('Y');

        $currentYear = (int) date('Y');
        for ($y = $currentYear - 5; $y <= $currentYear + 2; $y++) {
            $this->years[$y] = (string) $y;
        }
    }

    public function getFinancialData(): array
    {
        $orderQuery = Order::query()->where('status_payment', 'paid');

        if ($this->filterType === 'hari') {
            $orderQuery->whereDate('created_at', $this->selectedDate);
        } elseif ($this->filterType === 'bulan') {
            $orderQuery->whereMonth('created_at', $this->selectedMonth)->whereYear('created_at', $this->selectedYear);
        } elseif ($this->filterType === 'tahun') {
            $orderQuery->whereYear('created_at', $this->selectedYear);
        }

        $orders = $orderQuery->with('customer')->orderBy('created_at', 'desc')->get();

        $totalPemasukan = $orders->sum('total_price');
        $totalTransaksi = $orders->count();

        return [
            'orders' => $orders,
            'totalPemasukan' => $totalPemasukan,
            'totalTransaksi' => $totalTransaksi,
        ];
    }
}
