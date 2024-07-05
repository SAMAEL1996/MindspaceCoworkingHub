<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected static ?string $title = 'Staff Profiles';

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
