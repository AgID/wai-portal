Hi {{ $fullName }},

we aren't receiving any data from the {{ $website }}
you registered and successfully activated on the [{{ config('app.name') }}]({{ url('/') }}).

If there will be no changes in the next {{ $daysLeft }} days, it will be automatically archived.

Please verify that you have the tracking code is included on the website and
that it is working properly.

If that website isn't used anymore, you can just ignore this message or
you can manually archive the website by logging in the [{{ config('app.name') }}]({{ url('/') }}).
