<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDailySale extends ViewRecord
{
    protected static string $resource = DailySaleResource::class;

    protected static ?string $title = 'View Daily User';

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
