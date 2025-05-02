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

class InventoryItemTable extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public string $item;

    public array $items;

    protected $listeners = ['inventory-updated' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Inventory::where('item', $this->item)->latest('date'))
            ->heading($this->item)
            ->columns([
                TableColumns\TextColumn::make('date')
                    ->formatStateUsing(fn ($state): string => \Carbon\Carbon::parse($state)->format(config('app.date_time_format'))),
                TableColumns\TextColumn::make('quantity'),
                // TableColumns\TextColumn::make('status'),
            ])
            ->headerActions([
                TableActions\Action::make('update')
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
            ->actions([])
            ->bulkActions([])
            ->poll('1s');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-item-table');
    }
}
