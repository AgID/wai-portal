Hi {{ $user->name }},

the website "{{ $website->name }}" registered on [{{ config('app.name') }}]({{ url('/') }})
for your public administration ({{ $website->publicAdministration->name }}),
has been successfully reactivated.
