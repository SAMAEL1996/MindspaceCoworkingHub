<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex justify-between gap-x-3">
            <div class="flex items-center">
                <div>
                @if(!session('on_shift'))
                    Shift not started
                @else
                    Start time: {{ $timeCheck }}
                @endif

                @if(auth()->user()->checkLatestCashLogs())
                <br>
                <span style="color: red">
                    <em>Enter cash out amount before ending shift</em>
                </span>
                @endif
                </div>
            </div>
            <div class="flex items-center">
                @if(!session('on_shift'))
                    <x-filament::modal id="shiftModal" :close-by-clicking-away="false">
                        <x-slot name="trigger">
                            <x-filament::button>
                                Start Shift
                            </x-filament::button>
                        </x-slot>

                        <x-slot name="heading">
                            Start Shift
                        </x-slot>

                        <x-slot name="description">
                            Are you sure you want to start your shift?
                        </x-slot>

                        <x-slot name="footerActions">
                            <x-filament::button wire:click="startShift">
                                Yes
                            </x-filament::button>

                            <x-filament::button wire:click="testlang" color="gray">
                                No
                            </x-filament::button>
                        </x-slot>
                    </x-filament::modal>
                @else
                    @if(auth()->user()->checkLatestCashLogs())
                        <x-filament::button color="danger" disabled>
                            End Shift
                        </x-filament::button>
                    @else
                        <x-filament::modal id="shiftModal" :close-by-clicking-away="false">
                            <x-slot name="trigger">
                                <x-filament::button color="danger">
                                    End Shift
                                </x-filament::button>
                            </x-slot>

                            <x-slot name="heading">
                                End Shift
                            </x-slot>

                            <x-slot name="description">
                                Are you sure you want to end your shift?
                            </x-slot>

                            <x-slot name="footerActions">
                                <x-filament::button wire:click="endShift">
                                    Yes
                                </x-filament::button>

                                <x-filament::button wire:click="testlang" color="gray">
                                    No
                                </x-filament::button>
                            </x-slot>
                        </x-filament::modal>
                    @endif
                @endif
            </div>
        </div>
    </x-filament::section>

</x-filament-widgets::widget>