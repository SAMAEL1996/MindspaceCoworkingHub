<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlexiUsers extends ListRecords
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
