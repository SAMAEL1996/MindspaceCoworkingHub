<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="py-5">
            <x-filament::button wire:click="save">
                Save
            </x-filament::button>
        </div>
    </form>

    <div class="flex justify-center w-full">
        <x-filament::fieldset class="w-1/2">
            {{--<x-filament::input.wrapper wire:poll.1s="getSessionCard" disabled>--}}
            <x-filament::input.wrapper disabled>
                <x-filament::input
                    type="text"
                    wire:model="rfid_fetched"
                    class="text-center"
                    disabled
                />
            </x-filament::input.wrapper>

            <div class="flex justify-center w-full py-5">
                <div class="px-2">
                        <x-filament::button wire:click="fetchRfidScanned" color="info" class="">
                            Fetch
                        </x-filament::button>
                </div>
                <div class="px-2">
                    <x-filament::button wire:click="getRfidScanned" color="success" class="px-px">
                        Get
                    </x-filament::button>
                </div>
            </div>
        </x-filament::fieldset>
    </div>
    
    <x-filament-actions::modals />
</div>