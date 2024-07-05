<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlexiUser extends EditRecord
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
