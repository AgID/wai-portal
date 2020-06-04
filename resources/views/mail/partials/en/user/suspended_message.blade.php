Hello {{ $user->name }},

your account for {{ $publicAdministration->name }} on [{{ config('app.name') }}]({{ url('/') }}) has been suspended.

You can ask an administrator of your public administration to reactivate it.
