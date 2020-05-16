Hello {{ $user->name }},

you have been invited for your public administration
**{{ $publicAdministration->name }}** on [{{ config('app.name') }}]({{ url('/') }}).

You can log in and confirm in the administrative dashboard.

@component('mail::button', ['link' => route('publicAdministrations.show') ])
    Confirm invitation
@endcomponent
