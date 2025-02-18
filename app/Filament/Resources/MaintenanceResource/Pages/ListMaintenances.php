<?php

namespace App\Filament\Resources\MaintenanceResource\Pages;

use App\Filament\Resources\MaintenanceResource;
use App\Models\Maintenance;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;

class ListMaintenances extends ListRecords
{
    protected static string $resource = MaintenanceResource::class;
    protected static ?string $title = 'Maintenance';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add')
                ->label('Add')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Add Maintenance')
                ->fillForm([
                    'date' => \Carbon\Carbon::now()
                ])
                ->form([
                    FormComponents\TextInput::make('title'),
                    FormComponents\DatePicker::make('date'),
                ])
                ->action(function($data) {
                    $maintenance = Maintenance::create([
                        'title' => $data['title'],
                        'date' => $data['date'],
                    ]);

                    Notification::make()
                        ->title('Maintenance successfully created.')
                        ->success()
                        ->send();

                    return $maintenance;
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
