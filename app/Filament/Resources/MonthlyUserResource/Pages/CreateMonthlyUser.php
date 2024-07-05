<?php

namespace App\Filament\Resources\MonthlyUserResource\Pages;

use App\Filament\Resources\MonthlyUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateMonthlyUser extends CreateRecord
{
    protected static string $resource = MonthlyUserResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $data['date_start'] = \Carbon\Carbon::now();
        $data['date_finish'] = \Carbon\Carbon::now()->addMonth()->subDay();
        $data['is_active'] = false;
        $data['is_expired'] = false;
        $data['paid'] = true;
        $data['amount'] = 0.00;

        return static::getModel()::create($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Monthly user successfully created.';
    }
}
