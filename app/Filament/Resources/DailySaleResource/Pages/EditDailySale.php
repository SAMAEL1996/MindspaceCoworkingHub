<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDailySale extends EditRecord
{
    protected static string $resource = DailySaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['staff_name'] = $this->record->staffIn->user->name;
    
        return $data;
    }
}
