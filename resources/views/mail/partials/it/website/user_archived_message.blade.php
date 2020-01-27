Ciao {{ $user->name }},

@if($manually)
il sito "{{ $website->name }}" è stato archiviato come richiesto.
@else
negli ultimi {{ config('wai.archive_expire') }} giorni non abbiamo ricevuto
alcun dato dal sito "{{ $website->name }}" che hai
registrato ed attivato su [{{ config('app.name') }}]({{ url('/') }}).
Pertanto il sito è stato automaticamente archiviato.
@endif

Da questo momento in poi il traffico non sarà più tracciato.
