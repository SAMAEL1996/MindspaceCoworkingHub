<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions()->get()->pluck('id')->toArray();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if(count($data['permissions']) > 0) {
            $permissions = \App\Models\Permission::whereIn('id', $data['permissions'])->get();
            $record->syncPermissions($permissions);
        }
    
        return $record;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Role successfully updated.';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
