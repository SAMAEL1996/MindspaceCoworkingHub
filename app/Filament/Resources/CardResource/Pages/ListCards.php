<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false)
                ->visible(function() {
                    return auth()->user()->can('create cards');
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
