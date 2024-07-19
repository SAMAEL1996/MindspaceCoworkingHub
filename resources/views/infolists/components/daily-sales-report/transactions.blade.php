<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @livewire('daily-sales-report.transactions', [
                'report' => $getRecord()
            ])
    </div>
</x-dynamic-component>
