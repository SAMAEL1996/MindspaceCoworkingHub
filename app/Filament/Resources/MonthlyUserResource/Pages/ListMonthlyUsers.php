<?php

namespace App\Filament\Resources\MonthlyUserResource\Pages;

use App\Filament\Resources\MonthlyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MonthlyUser;

class ListMonthlyUsers extends ListRecords
{
    protected static string $resource = MonthlyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New')
                ->visible(function() {
                    return auth()->user()->can('create monthly-users');
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_expired', false))
                ->badge(MonthlyUser::query()->where('is_expired', false)->count()),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_expired', true)),
        ];
    }
}
