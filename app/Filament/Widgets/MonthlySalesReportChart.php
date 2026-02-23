<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MonthlySalesReportChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales';
    protected static ?string $pollingInterval = '10s';
    protected static string $color = 'warning';
    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $salesDatabase = \DB::table('sales')
            ->where('type', 'monthly')
            ->orderBy('year')
            ->orderByRaw("
                FIELD(month,
                    'January','February','March','April','May','June',
                    'July','August','September','October','November','December'
                )
            ")
            ->get();

        $labels = [];
        $totalSales = [];
        foreach($salesDatabase as $data) {
            $labels[] = $data->month . ' ' . $data->year;
            $totalSales[] = $data->total_sales;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Sales',
                    'data' => $totalSales,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    public static function canView(): bool
    {
        if (auth()->user()->hasRole('Super Administrator')) {
            return true;
        } else {
            return false;
        }
    }
}
