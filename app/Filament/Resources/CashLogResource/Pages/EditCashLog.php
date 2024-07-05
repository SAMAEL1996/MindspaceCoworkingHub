<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCashLog extends EditRecord
{
    protected static string $resource = CashLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
