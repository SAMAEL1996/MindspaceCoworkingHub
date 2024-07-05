<div>
    <form wire:submit.prevent="create" class="padding-bottom: 20px">
        {{ $this->form }}
    </form>
    
    <x-filament-actions::modals />

    <div class="flex justify-center w-full py-5">
        <x-filament::button wire:click="create">
            Submit
        </x-filament::button>
    </div>
</div>