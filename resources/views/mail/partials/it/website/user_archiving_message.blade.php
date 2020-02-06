Ciao {{ $user->name }},

non stiamo ricevendo alcun dato dal sito **{{ $website->name }}** che hai registrato
e attivato su [{{ config('app.name') }}]({{ url('/') }}).

Ti preghiamo di verificare:
- di aver incluso il codice di tracciamento nel sito;
- che il codice stia funzionando correttamente.

Per avere supporto su questo argomento, puoi consultare
la [guida di {{ config('app.name_short') }}]({{ config('site.kb.link') }}).

Se continueremo a non ricevere dati nei prossimi {{ $daysLeft }} giorni,
il sito sarà automaticamente archiviato.

Se il sito web non è più attivo, puoi ignorare questa mail
o accedere alla
[pagina gestione sito]({{ route('websites.show', ['website' => $website]) }})
per archiviare il sito manualmente.
