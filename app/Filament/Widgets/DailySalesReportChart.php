<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class DailySalesReportChart extends ChartWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static string $color = 'warning';
    protected int|string|array $columnSpan = 'full';
    public ?string $filter = 'total_sales';

    public function getHeading(): ?string
    {
        return Carbon::now()->format('F') . ' Sales';
    }

    protected function getData(): array
    {
        $now = Carbon::now();

        $salesDatabase = \DB::table('sales')
            ->where('type', 'daily')
            ->where('month', $now->copy()->format('F'))
            ->where('year', $now->copy()->format('Y'))
            ->orderBy('day')
            ->get();

        $label = 'Sales';
        $labels = [];
        $totalSales = [];
        foreach($salesDatabase as $data) {
            $labels[] = $data->month . ' ' . $data->day;

            switch($this->filter) {
                case 'total_sales':
                    $label = 'Sales';
                    $totalSales[] = $data->total_sales;
                    break;

                case 'daily_checkins':
                    $label = 'Daily Check-Ins';
                    $totalSales[] = $data->total_daily_users;
                    break;

                case 'flexi_users':
                    $label = 'Flexi Users';
                    $totalSales[] = $data->total_flexi_users;
                    break;

                case 'monthly_users':
                    $label = 'Monthly Users';
                    $totalSales[] = $data->total_monthly_users;
                    break;

                case 'conference_booking':
                    $label = 'Conference Booking';
                    $totalSales[] = $data->total_conference_users;
                    break;

                default:
            }
        }

        return [
            'datasets' => [
                [
                    'label' => $now->copy()->format('F') . ' ' . $label,
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
        return [
            'total_sales' => 'Total Sales',
            'daily_checkins' => 'Daily Check-Ins',
            'flexi_users' => 'Flexi Users',
            'monthly_users' => 'Monthly Users',
            'conference_booking' => 'Conference Booking',
        ];
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
