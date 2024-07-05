<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $staff = $this->getRecord();
        $data['name'] = $staff->user->name;
        $data['email'] = $staff->user->email;
        $data['contact_no'] = $staff->user->contact_no;
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Staff successfully updated.';
    }
}
