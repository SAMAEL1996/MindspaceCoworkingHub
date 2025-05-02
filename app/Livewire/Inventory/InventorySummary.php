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

class InventorySummary extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public array $items;

    protected $listeners = ['inventory-updated' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Inventory::whereIn('id', $this->items))
            ->columns([
                TableColumns\TextColumn::make('item')
                    ->color(fn ($record) => $record->status_color),
                TableColumns\TextColumn::make('quantity')
                    ->label('Stock Left')
                    ->formatStateUsing(function($record) {
                        return $record->quantity . ' '. $record->unit;
                    })
                    ->color(fn ($record) => $record->status_color),
                TableColumns\TextColumn::make('date')
                    ->label('Last Updated')
                    ->formatStateUsing(fn ($state): string => \Carbon\Carbon::parse($state)->format(config('app.date_time_format')))
                    ->color(fn ($record) => $record->status_color),
                TableColumns\SelectColumn::make('status')
                    ->options([
                        'In Stock' => 'In Stock',
                        'Running Out' => 'Running Out',
                        'Out of Stock' => 'Out of Stock'
                    ])
                    ->rules(['required']),
            ])
            ->actions([
                TableActions\Action::make('update')
                    ->button()
                    ->size(ActionSize::Small)
                    ->outlined()
                    ->form([
                        FormComponents\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                    ])
                    ->action(function($data, $record) {
                        $user = auth()->user();
                        $now = \Carbon\Carbon::now();

                        $inventory = Inventory::create([
                            'user_id' => $user->id,
                            'item' => $data['item'],
                            'quantity' => $data['quantity'],
                            'date' => $now,
                            'status' => $data['status'],
                            'is_active' => true
                        ]);

                        $this->loadItems();
                    })
            ])
            ->filters([])
            ->bulkActions([])
            ->poll('1s');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-summary');
    }
}
