<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Status Order';
    protected ?string $description = 'Persentase order berdasarkan status saat ini';
    protected static ?int $sort = 3;
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $statuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        $labels = [
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        $colors = [
            'pending'    => 'rgba(125, 86, 45, 0.8)',    // caramel (warning)
            'processing' => 'rgba(116, 88, 83, 0.8)',    // brown (primary)
            'shipped'    => 'rgba(59, 130, 246, 0.8)',    // blue
            'completed'  => 'rgba(16, 185, 129, 0.8)',    // emerald
            'cancelled'  => 'rgba(244, 63, 94, 0.8)',     // rose
        ];

        $data = [];
        $dataLabels = [];
        $dataColors = [];

        foreach ($statuses as $status) {
            $count = Order::where('status', $status)->count();
            if ($count > 0) {
                $data[] = $count;
                $dataLabels[] = $labels[$status];
                $dataColors[] = $colors[$status];
            }
        }

        if (empty($data)) {
            $data = [1];
            $dataLabels = ['Belum ada order'];
            $dataColors = ['rgba(210, 195, 190, 0.5)'];
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $dataColors,
                    'borderColor' => '#fff',
                    'borderWidth' => 2,
                    'hoverOffset' => 8,
                ],
            ],
            'labels' => $dataLabels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
