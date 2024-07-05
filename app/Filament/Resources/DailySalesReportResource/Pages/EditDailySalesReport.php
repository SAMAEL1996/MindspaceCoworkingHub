<?php

namespace App\Filament\Resources\DailySalesReportResource\Pages;

use App\Filament\Resources\DailySalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailySalesReport extends EditRecord
{
    protected static string $resource = DailySalesReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
