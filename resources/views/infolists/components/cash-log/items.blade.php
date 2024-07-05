<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @livewire('cash-log.items-table', [
                'cashLog' => $getRecord()
            ])
    </div>
</x-dynamic-component>
