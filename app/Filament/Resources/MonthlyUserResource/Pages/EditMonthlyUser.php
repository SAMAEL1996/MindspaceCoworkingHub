<?php

namespace App\Filament\Resources\MonthlyUserResource\Pages;

use App\Filament\Resources\MonthlyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlyUser extends EditRecord
{
    protected static string $resource = MonthlyUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}
