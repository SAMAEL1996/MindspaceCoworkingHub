<?php

namespace App\Filament\Resources\MonthlySalesReportResource\Pages;

use App\Filament\Resources\MonthlySalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMonthlySalesReport extends EditRecord
{
    protected static string $resource = MonthlySalesReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
