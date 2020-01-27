Ciao {{ $user->name }},

da più di {{ config('wai.archive_warning') }} giorni non stiamo ricevuto dati
dal sito registrato sul portale [{{ config('app.name') }}]({{ url('/') }}) come
sito istituzionale per la tua pubblica amministrazione.

Ti preghiamo di verificare:
- di aver incluso il codice di tracciamento nel sito,
- che il codice stia funzionando correttamente.

Per avere supporto su questo argomento, puoi consultare
la [guida dedicata di WAI](https://docs.italia.it/)
