Ciao {{ $fullName }},

il mese scorso non abbiamo ricevuto alcun dato dal sito {{ $website }}
che hai registrato ed attivato su [{{ config('app.name') }}]({{ url('/') }}).

Se non ci saranno cambiamenti nel corso del prossimo mese, sarà automaticamente archiviato.

Ti preghiamo di verificare di aver incluso il codice di tracciamento nel sito,
e che stia funzionando correttamente.

Se il sito web non è più attivo, puoi semplicemente ignorare questa mail
oppure entrare su [{{ config('app.name') }}]({{ url('/') }}) ed archiviarlo manualmente.
