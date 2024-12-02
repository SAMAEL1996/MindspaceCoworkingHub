<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('add')
                ->label('New')
                ->modalHeading('Create New Expenses')
                ->form([
                    FormComponents\TextInput::make('item')
                        ->required(),
                    FormComponents\TextInput::make('quantity')
                        ->numeric()
                        ->required(),
                    FormComponents\TextInput::make('amount')
                        ->numeric()
                        ->required(),
                ])
                ->action(function($data) {
                    $expense = \App\Models\Expense::create($data);

                    Notification::make()
                        ->title('Expenses successfully created.')
                        ->success()
                        ->send();

                    return $expense;
                })
                ->modalWidth(MaxWidth::Medium)
                ->visible(function() {
                    return auth()->user()->can('create expenses');
                }),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
