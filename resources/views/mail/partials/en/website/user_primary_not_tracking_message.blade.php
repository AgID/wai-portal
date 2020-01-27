Hi {{ $user->name }},

we have not received any data from the website you registered
on [{{ config('app.name') }}]({{ url('/') }}) as the primary
website for your public administration for more than {{ config('wai.archive_warning') }} days.

Please check:
- to have included the tracking code on the website,
- that the tracking code is working properly.

For more support on this topic, you can visit the [WAI guide](https://docs.italia.it/)
