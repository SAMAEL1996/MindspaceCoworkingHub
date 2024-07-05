<?php

namespace App\Filament\Resources\MonthlyUserResource\Pages;

use App\Filament\Resources\MonthlyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonthlyUser extends ViewRecord
{
    protected static string $resource = MonthlyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(function() {
                    return auth()->user()->can('edit monthly-users');
                }),
        ];
    }
}
