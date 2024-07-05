
<x-filament::modal id="shiftModal" :close-on-escape="false" :close-on-click-away="false">
    Form components here

    <x-slot name="actions">
        <button type="submit">
              Submit form
        </button>
    </x-slot>
</x-filament::modal>
@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.dispatch('open-modal', {
                id: 'shiftModal'
            });
        });
    </script>
@endpush