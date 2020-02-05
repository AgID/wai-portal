Hello {{ $user->name }},

@if($manually)
the website **{{ $website->name }}** has been archived as requested.
@else
in the last {{ config('wai.archive_expire') }} days we have not received
any data from the website **{{ $website->name }}** you have
registered and activated on [{{ config('app.name') }}]({{ url('/') }}),
therefore the site has been automatically archived.
@endif

From now on the traffic will no longer be tracked.

If you want to restore the website you can do so from
[site management page]({{ route('websites.show', ['website' => $website]) }}).
