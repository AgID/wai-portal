Hi {{ $user->name }},

we aren't receiving any data from the site "{{ $website->name }}"
that you registered and activated on the [{{ config('app.name') }}]({{ url('/') }}).

Please check:
- to have included the tracking code on the website,
- that the tracking code is working properly.

For more support on this topic, you can visit the [WAI guide](https://docs.italia.it/).

If we receive no data in the next {{ $daysLeft }} days,
the website will be automatically archived.

If the website isn't active anymore, you can ignore this message
or access [{{ config('app.name') }}]({{ url('/') }})
to manually archive the website.
