<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\MonthlyUser;
use App\Models\FlexiUser;
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
            Stat::make(
                    Carbon::now()->format('F') . ' Total Sales',
                    'PHP ' . self::getMonthTotalSales()
                )
                ->icon('heroicon-o-arrow-trending-up'),
            Stat::make(
                    'Total Sales',
                    'PHP ' . self::getTotalSales()
                )
                ->icon('heroicon-o-currency-dollar'),
        ];
    }

    public static function getMonthTotalSales()
    {
        $dailyPass = DailySale::withTrashed()
                                ->where('is_flexi', false)
                                ->where('is_monthly', false)
                                ->where('is_conference', false)
                                ->whereMonth('time_in', now()->month)
                                ->whereYear('time_in', now()->year)
                                ->get();

        $flexiPass = FlexiUser::where('paid', true)
                                ->whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->get();

        $monthlyPass = MonthlyUser::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->get();

        $conferencePass = Conference::whereMonth('created_at', now()->month)
                                ->whereYear('created_at', now()->year)
                                ->get();

        $total = $dailyPass->sum('amount_paid') + $flexiPass->sum('amount') + $monthlyPass->sum('amount') + $conferencePass->sum('payment');

        return number_format($total, 2);
    }

    public static function getTotalSales()
    {
        $dailyPass = DailySale::withTrashed()
                                ->where('is_flexi', false)
                                ->where('is_monthly', false)
                                ->where('is_conference', false)
                                ->sum('amount_paid');

        $flexiPass = FlexiUser::where('paid', true)
                                ->sum('amount');

        $monthlyPass = MonthlyUser::sum('amount');

        $conferencePass = Conference::sum('amount');

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
