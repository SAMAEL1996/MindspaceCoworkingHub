<?php

namespace App\Filament\Resources\MonthlySalesReportResource\Pages;

use App\Filament\Resources\MonthlySalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMonthlySalesReports extends ListRecords
{
    protected static string $resource = MonthlySalesReportResource::class;
    protected static ?string $title = 'Monthly Sales Report';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
