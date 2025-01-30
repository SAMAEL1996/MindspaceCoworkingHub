<?php

namespace App\Filament\Resources\DailySaleResource\Pages;

use App\Filament\Resources\DailySaleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;

class ViewDailySale extends ViewRecord
{
    protected static string $resource = DailySaleResource::class;

    protected static ?string $title = 'View Daily User';

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }

    protected function getHeaderActions(): array
{
    return [
        Actions\ActionGroup::make([
            Actions\Action::make('add-notes')
                ->label('Add Note')
                ->fillForm(fn ($record): array => [
                    'note' => $record->hasMeta('notes') ? $record->getMetaValue('notes') : null,
                ])
                ->form([
                    FormComponents\Textarea::make('note')
                        ->label('Note')
                        ->rows(5)
                        ->required()
                ])
                ->action(function($data, $record) {
                    $record->addOrUpdateMeta('notes', $data['note']);

                    Notification::make()
                        ->title('Notes successfully attached.')
                        ->success()
                        ->send();

                    return $record;
                }),
            Actions\EditAction::make()
                ->label('Edit')
                ->visible(auth()->user()->hasRole('Super Administrator'))
        ])
        ->label('Action')
        ->icon('heroicon-m-ellipsis-vertical')
        ->color('primary')
        ->button()
    ];
}
}
