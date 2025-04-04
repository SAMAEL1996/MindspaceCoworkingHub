<div>
    @if(!$booked)
        <form wire:submit.prevent="create" class="padding-bottom: 20px">
            {{ $this->form }}
        </form>
        
        <x-filament-actions::modals />

        <div style="padding-top: 20px" class="flex w-full justify-center">
            <x-filament::button wire:click="create" wire:loading.class="disabled" class="mr-2">
                Book Now
            </x-filament::button>
        </div>
    @else
        <x-filament::section>
            <div style="text-align: center;">
            Your booking has been submitted and is now pending for approval. <br><br>
            Please check your email or phone for confirmation. <br><br>
            Thank you!
            </div>
        </x-filament::section>
    @endif
</div>