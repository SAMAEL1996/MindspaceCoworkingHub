<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FlexiUser;

class ListFlexiUsers extends ListRecords
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge(FlexiUser::query()->where('status', true)->count()),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false))
                ->badge(FlexiUser::query()->where('status', false)->count()),
            'all' => Tab::make('All')
                ->badge(FlexiUser::query()->count()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
