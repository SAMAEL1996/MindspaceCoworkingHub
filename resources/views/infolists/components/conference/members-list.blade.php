<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div>
        @livewire('conference.members-list', [
                'conference' => $getRecord()
            ])
    </div>
</x-dynamic-component>
