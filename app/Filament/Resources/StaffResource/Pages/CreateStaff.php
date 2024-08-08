<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $card = \App\Models\Card::find($data['card_id']);
        if(!$card) {
            Notification::make()
                ->title('Card not found!')
                ->warning()
                ->send();
    
            $this->halt();
        }
        
        $dataUser = [
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_no' => $data['contact_no'],
            'password' => bcrypt($data['password']),
            'is_staff' => true,
            'status' => true
        ];
        $user = \App\Models\User::create($dataUser);

        $dataStaff = [
            'user_id' => $user->id,
            'card_id' => $card->id,
            'personal_email' => $data['personal_email'],
            'is_active' => true,
            'emergency_contact_person' => $data['emergency_contact_person'],
            'emergency_relationship' => $data['emergency_relationship'],
            'emergency_contact_no' => $data['emergency_contact_no'],
        ];
        $staff = static::getModel()::create($dataStaff);
        $user->assignRole('Staff');

        return $staff;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Staff successfully created.';
    }
}
