Hello {{ $user->name }},

the website **{{ $website->name }}** of your public administration
**{{ $website->publicAdministration->name }}**, has been successfully reactivated
on [{{ config('app.name') }}]({{ url('/') }}).
