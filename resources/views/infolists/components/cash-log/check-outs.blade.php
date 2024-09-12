<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @livewire('cash-log.check-outs', [
                'cashLog' => $getRecord()
            ])
    </div>
</x-dynamic-component>
