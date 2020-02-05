Hello {{ $user->name }},

your profile on [{{ config('app.name') }}]({{ url('/') }}) has been
successfully activated.

You can now use all the features of
[{{ config('app.name_short') }}]({{ url('/') }}).
