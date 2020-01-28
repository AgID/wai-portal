Ciao {{ $user->name }},

non abbiamo ancora ricevuto dati dal sito "{{ $website->name }}"
registrato sul portale [{{ config('app.name') }}]({{ url('/') }}).

Ti preghiamo di verificare:
- di aver incluso il codice di tracciamento nel sito,
- che il codice stia funzionando correttamente.

Per avere supporto su questo argomento, puoi consultare
la [guida dedicata di WAI]({{ config('site.kb.link') }})

Se continueremo a non ricevere dati nei prossimi {{ config('wai.purge_expiry') - config('wai.purge_warning') }}
giorni, il sito sarà eliminato.
