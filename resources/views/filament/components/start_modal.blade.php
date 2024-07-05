
<x-confirmation-modal wire:model="showModal">
    <x-slot name="title">
        Delete Account
    </x-slot>

    <x-slot name="content">
        Are you sure you want to delete your account? Once your account is deleted, all data will be permanently deleted.
    </x-slot>

    <x-slot name="footer">

        <x-danger-button class="ml-2" wire:loading.attr="disabled">
            Delete Account
        </x-danger-button>
    </x-slot>
</x-confirmation-modal>