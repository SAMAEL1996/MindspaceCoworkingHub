<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use App\Models\DailySale;

class CreateDailySale extends CreateRecord
{
    protected static string $resource = DailySaleResource::class;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        $discount = 0;
        if($data['apply_discount']) {
            $discount = $data['discount'];
        }

        $saleData = [
            'date' => $data['date'],
            'time_in_staff_id' => $data['time_in_staff_id'],
            'card_id' => $data['card_id'],
            'name' => $data['name'],
            'description' => $data['description'],
            'apply_discount' => $data['apply_discount'],
            'discount' => $discount,
            'time_in' => \Carbon\Carbon::now()->addMinutes(10),
            'status' => true,
            'is_monthly' => false
        ];

        $dailyPass = static::getModel()::create($saleData);

        $dailyPass->addCheckInToSalesReport();

        return $dailyPass;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
