<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\MonthlyUser;
use App\Models\DailySale;
use App\Models\Conference;
use Carbon\Carbon;

class SalesCount extends BaseWidget
{
    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '1';

    protected function getColumns(): int
    {
        return 2;
    }

    protected function getCards(): array
    {
        return [
            Card::make(
                    \Carbon\Carbon::now()->format('F') . ' Total Sales',
                    'PHP ' . self::getMonthTotalSales()
                )
                ->icon('heroicon-o-arrow-trending-up'),
            Card::make(
                    'Total Sales',
                    'PHP ' . self::getTotalSales()
                )
                ->icon('heroicon-o-currency-dollar'),
        ];
    }

    public static function getMonthTotalSales()
    {
        $dailyPass = \App\Models\DailySale::whereMonth('time_in', Carbon::now()->month)
                                            ->whereYear('time_in', Carbon::now()->year)
                                            ->where('is_flexi', false)
                                            ->where('is_monthly', false);

        $flexiPass = \App\Models\FlexiUser::whereMonth('created_at', Carbon::now()->month)
                                            ->whereYear('created_at', Carbon::now()->year)
                                            ->where('paid', true);

        $monthlyPass = \App\Models\MonthlyUser::whereMonth('created_at', Carbon::now()->month)
                                            ->whereYear('created_at', Carbon::now()->year);

        $conferencePass = \App\Models\Conference::whereMonth('created_at', Carbon::now()->month)
                                            ->whereYear('created_at', Carbon::now()->year)
                                            ->where('has_reservation_fee', true);

        $total = $dailyPass->sum('amount_paid') + $flexiPass->sum('amount') + $monthlyPass->sum('amount') + $conferencePass->sum('payment');
        
        return number_format($total, 2);
    }

    public static function getTotalSales()
    {
        $dailyPass = \App\Models\DailySale::where('is_flexi', false)
                                            ->where('is_monthly', false)
                                            ->sum('amount_paid');

        $flexiPass = \App\Models\FlexiUser::where('paid', true)->sum('amount');

        $monthlyPass = \App\Models\MonthlyUser::sum('amount');

        $conferencePass = \App\Models\Conference::where('has_reservation_fee', true)->sum('payment');

        $total = $dailyPass + $flexiPass + $monthlyPass + $conferencePass;
        return number_format($total, 2);
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
