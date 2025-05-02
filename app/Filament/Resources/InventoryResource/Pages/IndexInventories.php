<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use Filament\Forms\Components as FormComponents;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;

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
            Actions\Action::make('add-new')
                ->label('Add New')
                ->icon('heroicon-m-plus-circle')
                ->modalHeading('Guest Check-In')
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    FormComponents\TextInput::make('item')
                        ->required()
                        ->autocomplete(false)
                        ->datalist(function() {
                            return $this->items;
                        }),
                    FormComponents\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    FormComponents\Select::make('status')
                        ->options([
                            'In Stock' => 'In Stock',
                            'Running Out' => 'Running Out',
                            'Out of Stock' => 'Out of Stock'
                        ])
                        ->native(false)
                        ->required()
                ])
                ->action(function($data) {
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

                    Notification::make()
                        ->title('Success')
                        ->body('Inventory successfully updated.')
                        ->success()
                        ->send();

                    $this->loadItems();

                    $this->dispatch('inventory-updated');
                })
        ];
    }
}
