<div>
    <form wire:submit.prevent="create" class="padding-bottom: 20px">
        {{ $this->form }}
    </form>
    
    <x-filament-actions::modals />

    <div style="padding-top: 20px">
        <x-filament::button type="submit" wire:loading.class="disabled" class="mr-2">
            Create
        </x-filament::button>
    </div>
</div>