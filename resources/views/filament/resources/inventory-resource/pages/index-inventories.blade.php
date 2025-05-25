<x-filament-panels::page>
    @livewire('inventory.inventory-summary', ['items' => $items])
    {{-- <div>
        @livewire(\App\Filament\Resources\InventoryResource\Widgets\InventoryStatOverview::class, ['items' => $items])
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($items as $item)
            @livewire('inventory.inventory-item-table', ['item' => $item, 'items' => $items], key($item))
        @endforeach
    </div> --}}
</x-filament-panels::page>
