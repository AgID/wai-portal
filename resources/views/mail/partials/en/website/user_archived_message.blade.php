Hi {{ $user->name }},

@if($manually)
the website "{{ $website->name }}" has been archived as requested.
@else
in last {{ config('wai.archive_expire') }} days we did not received any data
from the website "{{ $website->name }}"
which was registered and activated on [{{ config('app.name') }}]({{ url('/') }}).
For this reason, it has been automatically archived.
@endif

From now on traffic on the website will not be tracked.
