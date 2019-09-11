Ciao {{ $fullName }},

@if ($manual)
il sito {{ $website }} è stato archiviato come richiesto.
@else
negli ultimi {{ $expire }} giorni, non abbiamo ricevuto alcun dato dal sito
{{ $website }} che hai registrato ed attivato su
[{{ config('app.name') }}]({{ url('/') }}), pertanto è stato automaticamente
archiviato.
@endif

Da adesso in poi il traffico non sarà più tracciato.
