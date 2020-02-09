Ciao {{ $user->name }},

il sito web **{{ $website->name }}** è stato
rimosso perché non abbiamo ricevuto dati entro {{ config('wai.purge_expiry') }} giorni
dalla registrazione su [{{ config('app.name') }}]({{ url('/') }}).
