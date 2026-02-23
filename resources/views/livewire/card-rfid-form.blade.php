<div wire:poll.1s="checkScannedRfid">
    <form wire:submit="save">
        {{ $this->form }}

        <div class="d-flex justify-content-center py-5 gap-4">
            <x-filament::button wire:click="save">
                Save
            </x-filament::button>
            <x-filament::button href="{{ \App\Filament\Resources\CardResource::getUrl('index') }}" tag="a" color="gray">
                Cancel
            </x-filament::button>
        </div>
    </form>
    
    <x-filament-actions::modals />
</div>