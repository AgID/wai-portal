Hi {{ $user->name }},

your public administration ({{ $publicAdministration->name }}) has been
successfully activated on [{{ config('app.name') }}]({{ url('/') }}).

From now on traffic data of the institutional website will be gathered
and processed on the platform.

If you need support on any topic related to {{ config('app.name_short') }},
you can visit the [guide](https://docs.italia.it/).
