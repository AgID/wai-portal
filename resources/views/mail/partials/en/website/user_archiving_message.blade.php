Hello {{ $user->name }},

we're not getting any data from the **{{ $website->name }}** website that you
registered and activated on [{{ config('app.name') }}]({{ url('/') }}).

Please check:
- that you have included the tracking code in the website;
- that the code is working properly.

For support on this topic, you can consult
the [{{ config('app.name_short') }} guide]({{ config('site.kb.link') }}).

If we continue to receive no data in the next {{ $daysLeft }} days,
the website will be automatically archived.

If the website is no longer active, you can ignore this email
or access the
[site management page]({{ route('websites.show', ['website' => $website]) }})
to store the website manually.
