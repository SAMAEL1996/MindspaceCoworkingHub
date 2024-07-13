<div>
    <div class="w-100 flex justify-end mb-5">
        @if($viewList)
            <x-filament::button wire:click="changeView('list')" outlined>
                View Calendar
            </x-filament::button>
        @else
            <x-filament::button wire:click="changeView('calendar')" outlined>
                View List
            </x-filament::button>
        @endif
    </div>

    @if($viewList)
        <div class="fi-tabs flex max-w-full gap-x-1 overflow-x-auto mx-auto py-5 shadow-sm" role="tablist">
            <x-filament::tabs label="Content tabs">
                
            <x-filament::tabs.item
                    :active="$activeTab === 'upcoming'"
                    wire:click="setActiveTab('upcoming')"
                >
                    Upcoming
                </x-filament::tabs.item>
                
                <x-filament::tabs.item
                    :active="$activeTab === 'past'"
                    wire:click="setActiveTab('past')"
                >
                    Ended
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    :active="$activeTab === 'all'"
                    wire:click="setActiveTab('all')"
                >
                    All
                </x-filament::tabs.item>
                
            </x-filament::tabs>
        </div>
        {{ $this->table }}

        <script>
            document.addEventListener('refresh-table', () => {
                @this.call('$refresh');
            });
        </script>
    @else
        @livewire(\App\Filament\Widgets\CalendarWidget::class)
    @endif
</div>