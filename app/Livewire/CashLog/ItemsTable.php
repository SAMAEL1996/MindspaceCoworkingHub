<?php

namespace App\Livewire\CashLog;

use Livewire\Component;
use App\Filament\Resources\ConferenceResource;
use App\Models\CashLog;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions as TableActions;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components as InfolistComponents;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Components as FormComponents;
use Filament\Notifications\Notification;

class ItemsTable extends Component implements HasForms, HasInfolists, HasTable
{
    use InteractsWithInfolists;
    use InteractsWithTable;
    use InteractsWithForms;

    public CashLog $cashLog;

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn (): HasMany => $this->cashLog->items())
            ->headerActions([
                // TableActions\Action::make('add-item')
                //     ->label('Add Item')
                //     ->modalHeading('Add Cashlog Items')
                //     ->form([
                //         FormComponents\Select::make('type')
                //             ->label('Item type')
                //             ->options([
                //                 'in' => 'Credit',
                //                 'out' => 'Debit',
                //             ])
                //             ->required()
                //             ->native(false),
                //         FormComponents\TextInput::make('amount')
                //             ->label('Amount')
                //             ->numeric()
                //             ->minValue(1)
                //             ->required(),
                //         FormComponents\Textarea::make('description')
                //             ->required()
                //             ->rows(5)
                //     ])
                //     ->action(function (array $data) {
                //         $item = $this->cashLog->items()->create([
                //             'in' => $data['type'] == 'in' ? $data['amount'] : 0.00,
                //             'out' => $data['type'] == 'out' ? $data['amount'] : 0.00,
                //             'description' => $data['description']
                //         ]);

                //         Notification::make()
                //             ->title('Item successfully added.')
                //             ->success()
                //             ->send();

                //         return $item;
                //     }),
            ])
            ->columns([
                TableColumns\TextColumn::make('in')
                    ->label('Credit')
                    ->money('PHP'),
                TableColumns\TextColumn::make('out')
                    ->label('Debit')
                    ->money('PHP'),
                TableColumns\TextColumn::make('description'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.cash-log.items-table');
    }
}
