<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFlexiUser extends ViewRecord
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
