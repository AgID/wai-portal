Ciao {{ $user->name }},

ti informiamo che il tuo profilo su [{{ config('app.name') }}]({{ url('/') }}) è stato
correttamente attivato.

Da questo momento potrai accedere alle funzionalità di
[{{ config('app.name_short') }}]({{ url('/') }}).
