<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Livewire\Livewire;

class IndexInventories extends Page
{
    protected static string $resource = InventoryResource::class;

    protected static string $view = 'filament.resources.inventory-resource.pages.index-inventories';

    protected static ?string $title = 'Inventories';

    public array $items = [];

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadItems(): void
    {
        $this->items = Inventory::query()
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('item')
            ->pluck('id')
            ->toArray();
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save-inventory')
                ->label('Save')
                ->requiresConfirmation()
                ->color('success')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function() {
                    $now = Carbon::now();

                    $inventories = Inventory::whereIn('id', $this->items)->get();
                    foreach($inventories as $inventory) {
                        $inventory->addOrUpdateMeta(
                            $now->copy()->toDateString(),
                            [
                                'user_id' => $inventory->user_id,
                                'quantity' => $inventory->quantity,
                                'status' => $inventory->status,
                                'date' => $inventory->date
                            ]
                        );
                    }

                    Notification::make()
                        ->title('Success')
                        ->body('Inventories successfully saved.')
                        ->success()
                        ->send();
                })
                ->visible(fn () => Inventory::where('is_active', true)->count() > 0),
            Actions\Action::make('add-new')
                ->label('Add New')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Add Inventory Item')
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel('Add')
                ->form([
                    FormComponents\TextInput::make('item')
                        ->required()
                        ->autocomplete(false)
                        ->datalist(function() {
                            return Inventory::whereIn('id', $this->items)->get()->pluck('item')->toArray();
                        }),
                    FormComponents\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    FormComponents\TextInput::make('unit')
                        ->required()
                ])
                ->action(function($data) {
                    $user = auth()->user();
                    $now = Carbon::now();

                    $inventory = Inventory::create([
                        'user_id' => $user->id,
                        'item' => $data['item'],
                        'quantity' => $data['quantity'],
                        'unit' => $data['unit'],
                        'date' => $now->copy(),
                        'status' => 'In Stock',
                        'is_active' => true
                    ]);

                    $inventory->addOrUpdateMeta(
                        $now->copy()->toDateString(),
                        [
                            'user_id' => $user->id,
                            'quantity' => $data['quantity'],
                            'status' => 'In Stock',
                            'date' => $now->copy()
                        ]
                    );

                    Notification::make()
                        ->title('Success')
                        ->body('Inventory successfully created.')
                        ->success()
                        ->send();

                    $this->loadItems();

                    $this->dispatch('inventory-updated', items: $this->items);
                })
        ];
    }
}
