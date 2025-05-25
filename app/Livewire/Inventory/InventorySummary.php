<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns as TableColumns;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions as TableActions;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;

class InventorySummary extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public array $items;

    protected $listeners = ['inventory-updated' => 'updateItems'];

    public function updateItems($items)
    {
        $this->items = $items;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => Inventory::query()->whereIn('id', $this->items))
            ->columns([
                TableColumns\TextColumn::make('id')
                    ->label('ID')
                    ->color(fn ($record) => $record->status_color)
                    ->visible(auth()->user()->hasRole('Super Administrator')),
                TableColumns\TextColumn::make('item')
                    ->color(fn ($record) => $record->status_color)
                    ->searchable(),
                TableColumns\TextColumn::make('quantity')
                    ->label('Stock Left')
                    ->formatStateUsing(function($record) {
                        return $record->quantity;
                    })
                    ->color(fn ($record) => $record->status_color),
                TableColumns\TextColumn::make('unit')
                    ->color(fn ($record) => $record->status_color),
                TableColumns\TextColumn::make('date')
                    ->label('Last Updated')
                    ->formatStateUsing(fn ($state): string => \Carbon\Carbon::parse($state)->format(config('app.date_format')))
                    // ->description(fn ($state): string => \Carbon\Carbon::parse($state)->format(config('app.time_format')))
                    ->color(fn ($record) => $record->status_color),
                TableColumns\SelectColumn::make('status')
                    ->options([
                        'In Stock' => 'In Stock',
                        'Running Out' => 'Running Out',
                        'Out of Stock' => 'Out of Stock'
                    ])
                    ->sortable(query: function ($query, $direction) {
                        return $query->orderByRaw("
                            CASE status
                                WHEN 'In Stock' THEN 1
                                WHEN 'Running Out' THEN 2
                                WHEN 'Out of Stock' THEN 3
                                ELSE 4
                            END $direction
                        ");
                    })
                    ->rules(['required']),
            ])
            ->actions([
                TableActions\Action::make('show')
                    ->label('Show Record')
                    ->button()
                    ->size(ActionSize::Small)
                    ->outlined()
                    ->modalWidth(MaxWidth::FourExtraLarge)
                    ->modalHeading(fn ($record) => $record->item)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->visible(false),
                TableActions\Action::make('update')
                    ->label('Update Stock')
                    ->button()
                    ->size(ActionSize::Small)
                    ->outlined()
                    ->modalWidth(MaxWidth::Small)
                    ->modalHeading(fn ($record) => $record->item . ' stock')
                    ->form([
                        FormComponents\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                    ])
                    ->action(function($data, $record) {
                        $user = auth()->user();
                        $now = \Carbon\Carbon::now();

                        $record->update([
                            'user_id' => $user->id,
                            'quantity' => $data['quantity'],
                            'date' => $now->copy()
                        ]);

                        Notification::make()
                            ->title('Success')
                            ->body('Inventory successfully updated.')
                            ->success()
                            ->send();

                        return $record;
                    })
            ])
            ->filters([])
            ->bulkActions([])
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.inventory.inventory-summary');
    }
}
