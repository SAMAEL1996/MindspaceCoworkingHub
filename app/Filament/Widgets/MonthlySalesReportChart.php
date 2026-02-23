<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class MonthlySalesReportChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales';
    protected static ?string $pollingInterval = '10s';
    protected static string $color = 'warning';
    protected int|string|array $columnSpan = 'full';
    public ?string $filter = 'All';

    public function getHeading(): ?string
    {
        if($this->filter == 'All') {
            return 'Monthly Sales';
        } else {
            return $this->filter . ' Monthly Sales';
        }
    }

    protected function getData(): array
    {
        $query = \DB::table('sales')
            ->where('type', 'monthly');

        if($this->filter == 'All') {
            $query->orderBy('year');
        } else {
            $query->where('year', $this->filter);
        }

        $salesDatabase = $query->orderByRaw("
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
        return 'bar';
    }

    protected static ?array $options = [
        'plugins' => [
            'legend' => [
                'display' => false,
            ],
        ],
    ];

    public function getFilters(): ?array
    {
        $years = \DB::table('sales')
            ->select('year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year', 'year') // key => value
            ->toArray();

        $years = ['All' => 'All'] + $years;

        return $years;
    }

    public static function canView(): bool
    {
        if (auth()->user()->hasRole('Super Administrator')) {
            return true;
        } else {
            return false;
        }
    }
}
