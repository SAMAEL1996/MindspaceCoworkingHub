<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components as FormComponents;
use Livewire\Component;

class ViewCard extends ViewRecord
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scan-rfid')
                ->modalHeading('Scan RFID')
                ->form([
                    FormComponents\TextInput::make('rfid')
                        ->label('RFID')
                        ->required()
                        ->default(session('card', '')), // Set the default value from the session
                ])
                ->visible(auth()->user()->hasRole('Super Administrator')),
        ];
    }
}
