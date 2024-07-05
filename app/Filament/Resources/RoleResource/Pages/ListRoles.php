<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Closure;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function() {
                    return auth()->user()->can('create roles');
                }),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
