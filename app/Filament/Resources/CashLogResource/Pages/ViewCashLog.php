<?php

namespace App\Filament\Resources\CashLogResource\Pages;

use App\Filament\Resources\CashLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;

class ViewCashLog extends ViewRecord
{
    protected static string $resource = CashLogResource::class;

    public function getTitle(): string
    {
        return $this->record->user->name . ' Cash Log: ' . \Carbon\Carbon::parse($this->record->date_cash_in)->format(config('app.date_format'));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add-item')
                ->label('Add Item')
                ->modalHeading('Add Cashlog Items')
                ->form([
                    FormComponents\Select::make('type')
                        ->label('Item type')
                        ->options([
                            'in' => 'Credit',
                            'out' => 'Debit',
                        ])
                        ->required()
                        ->native(false),
                    FormComponents\TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->minValue(1)
                        ->required(),
                    FormComponents\Textarea::make('description')
                        ->required()
                        ->rows(5)
                ])
                ->action(function (array $data) {
                    $item = $this->record->items()->create([
                        'in' => $data['type'] == 'in' ? $data['amount'] : 0.00,
                        'out' => $data['type'] == 'out' ? $data['amount'] : 0.00,
                        'description' => $data['description']
                    ]);

                    Notification::make()
                        ->title('Item successfully added.')
                        ->success()
                        ->send();

                    return redirect()->to(CashLogResource::getUrl('view', ['record' => $this->record]));
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
