Hello {{ $user->name }},

please note that the *URL* field of the website **{{ $website->name }}** of your
public administration ({{ $publicAdministration }}), has been modified on
[{{ config('app.name') }}]({{ url('/') }}).

You can view the changes by accessing the
[site management page]({{ route('websites.show', ['website' => $website]) }}).

