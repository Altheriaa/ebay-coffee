<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopProductsChartWidget extends ChartWidget
{
    protected ?string $heading = 'Produk Terlaris';
    protected ?string $description = 'Top 5 produk berdasarkan jumlah terjual bulan ini';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $now = Carbon::now();

        $topProducts = OrderItem::query()
            ->select('product_id', DB::raw('SUM(qty) as total_qty'))
            ->whereHas('order', function ($q) use ($now) {
                $q->where('status_payment', 'paid')
                  ->whereMonth('created_at', $now->month)
                  ->whereYear('created_at', $now->year);
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'rgba(62, 39, 35, 0.85)',
            'rgba(116, 88, 83, 0.85)',
            'rgba(125, 86, 45, 0.85)',
            'rgba(163, 94, 79, 0.85)',
            'rgba(192, 125, 110, 0.85)',
        ];

        foreach ($topProducts as $i => $item) {
            $productName = $item->product->nama_product ?? 'Produk #' . $item->product_id;
            $labels[] = mb_strlen($productName) > 18 ? mb_substr($productName, 0, 18) . '…' : $productName;
            $data[] = $item->total_qty;
        }

        if (empty($data)) {
            $labels = ['Belum ada data'];
            $data = [0];
            $colors = ['rgba(210, 195, 190, 0.5)'];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Terjual',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => '#fff',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
