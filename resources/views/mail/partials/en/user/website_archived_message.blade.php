Hi {{ $fullName }},

@if ($manual)
the website {{ $website }} has been archived as requested.
@else
in the last {{ $expire }} days, we haven't received any data from the {{ $website }}
you registered and successfully activated on the [{{ config('app.name') }}]({{ url('/') }}),
therefore the website has been automatically archived.
@endif

From now on, the traffic will no longer be tracked.
