Ciao {{ $user->full_name }},

your email address for **{{ $publicAdministration->name }}** on [{{ config('app.name') }}]({{ url('/') }}) has been modified.

The updated email address is {{ $updatedEmail }}.
