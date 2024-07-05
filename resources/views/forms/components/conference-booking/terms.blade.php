<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <x-filament::fieldset>
            <x-slot name="label">
                Terms & Conditions
            </x-slot>
            
            <div style="color: #808080">
            All guests will be required to check in at the time of arrival. <br><br>

            1 hour holding period for the reservation. If any of the party did not arrive within the holding time, reservation will be automatically cancelled. <br><br>

            We will be required to collect 50% advance payment for the reservation fee. Remaining 50% will be collected after check out. <br><br>

            Advance payment is non-refundable as we consider your booking SOLD and will not accept future bookings. <br><br>

            Please be advise that rebooking/changing of date and time will be subject to meeting room’s availability. <br><br>

            Please inform our staff in advance if you want to extend time and/or would like to use the coworking space (charged separately). <br><br>
            </div>
        </x-filament::fieldset>

        <label>
            <x-filament::input.checkbox wire:model="termsCondition" />

            <span>
                I accept and agree to the Terms and Conditions.
            </span>
        </label>
    </div>
</x-dynamic-component>
