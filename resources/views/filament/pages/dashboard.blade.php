<x-filament-panels::page class="fi-dashboard-page">
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <x-filament-widgets::widgets
        :columns="$this->getColumns()"
        :data="
            [
                ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                ...$this->getWidgetData(),
            ]
        "
        :widgets="$this->getVisibleWidgets()"
    />

    <x-filament::modal id="shiftModal" :close-by-clicking-away="false">
        <x-filament::button wire:click="startShift">
            Start Your Shift
        </x-filament::button>

        <x-filament::button wire:click="justVisit" color="gray">
            Just Visit
        </x-filament::button>
    </x-filament::modal>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                if({{ auth()->user()->is_staff }} && !{{json_encode($staffLogin)}}) {
                    // @this.dispatch('open-modal', {
                    //     id: 'shiftModal'
                    // });
                }
            });
        </script>
    @endpush
</x-filament-panels::page>