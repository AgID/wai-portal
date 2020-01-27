Hi {{ $user->name }},

we haven't yet received data from the website "{{ $website->name }}"
registered on [{{ config('app.name') }}]({{ url('/') }}).

Please check:
- to have included the tracking code on the website,
- that the tracking code is working properly.

For more support on this topic, you can visit the [WAI guide](https://docs.italia.it/).

If we receive no data in the next {{ config('wai.purge_expiry') - config('wai.purge_warning') }} days,
the website will be removed.
