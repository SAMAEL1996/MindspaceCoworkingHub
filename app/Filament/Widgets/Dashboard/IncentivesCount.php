<?php

namespace App\Filament\Widgets\Dashboard;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\MonthlyUser;
use App\Models\DailySale;
use App\Models\FlexiUser;
use App\Models\Conference;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class IncentivesCount extends BaseWidget
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
            Card::make('Average Daily Pass Check-in', DailySale::getAvarageSalesPerMonth())
                ->icon('heroicon-o-user-group')
                ->description(new HtmlString('<b>TARGET</b>: 15 average daily users.')),
            Card::make(
                    'Add New Flexi Pass',
                    FlexiUser::whereMonth('start_at', Carbon::now()->month)
                                ->whereYear('start_at', Carbon::now()->year)
                                ->count()
                )
                ->icon('heroicon-o-users')
                ->description(new HtmlString('<b>TARGET</b>: 40 flexi pass.')),
            Card::make(
                    'Add New Monthly Pass',
                    MonthlyUser::whereMonth('date_start', Carbon::now()->month)
                                ->whereYear('date_start', Carbon::now()->year)
                                ->count()
                )
                ->icon('heroicon-o-user-circle')
                ->description(new HtmlString('<b>TARGET</b>: 18 monthly users.')),
            Card::make(
                    'Completed Meeting Room Booking',
                    Conference::whereMonth('start_at', Carbon::now()->month)
                                ->whereYear('start_at', Carbon::now()->year)
                                ->where('status', 'finished')
                                ->count()
                )
                ->icon('heroicon-o-clipboard-document-check')
                ->description(new HtmlString('<b>TARGET</b>: 15 completed meeting room booking.')),
        ];
    }
}
