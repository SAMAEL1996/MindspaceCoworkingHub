<?php

namespace App\Filament\Resources\MonthlySalesReportResource\Pages;

use App\Filament\Resources\MonthlySalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMonthlySalesReport extends ViewRecord
{
    protected static string $resource = MonthlySalesReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
