Ciao {{ $user->name }},

@if($manually)
il sito **{{ $website->name }}** è stato archiviato come richiesto.
@else
negli ultimi {{ config('wai.archive_expire') }} giorni non abbiamo ricevuto
alcun dato dal sito **{{ $website->name }}** che hai
registrato ed attivato su [{{ config('app.name') }}]({{ url('/') }}),
pertanto il sito è stato automaticamente archiviato.
@endif

Da questo momento in poi il traffico non sarà più tracciato.

Se vuoi ripristinare il sito web puoi farlo dalla
[pagina gestione sito]({{ route('websites.show', ['website' => $website]) }}).
