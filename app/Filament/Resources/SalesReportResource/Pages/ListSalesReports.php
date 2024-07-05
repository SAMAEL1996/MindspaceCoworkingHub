<?php

namespace App\Filament\Resources\SalesReportResource\Pages;

use App\Filament\Resources\SalesReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesReports extends ListRecords
{
    protected static string $resource = SalesReportResource::class;

    protected static ?string $title = 'My Sales';

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
