<?php

namespace App\Filament\Resources\InventoryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Inventory;

class InventoryStatOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '1s';

    protected $listeners = ['inventory-updated' => '$refresh'];

    public $items;

    protected function getStats(): array
    {
        $stats = [];
        foreach($this->items as $item) {
            $inventory = Inventory::where('item', $item)->latest()->first();

            if($inventory->status == 'In Stock') {
                $color = 'success';
            } elseif($inventory->status == 'Running Out') {
                $color = 'warning';
            } else {
                $color = 'danger';
            }

            $stats[] = Stat::make($item, $inventory->quantity)
                ->color($color);
        }

        return $stats;
    }
}
