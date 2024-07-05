<x-mail::message>
Test email for {{ $staff->user->name }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>