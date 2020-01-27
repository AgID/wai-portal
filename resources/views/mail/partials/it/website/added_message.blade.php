Ciao {{ $user->name }},

il sito web "{{ $website->name }}" è stato
registrato correttamente su [{{ config('app.name') }}]({{ url('/') }}).

@include('mail.partials.it.website.tracking_instructions', ['javascriptSnippet' => $javascriptSnippet])

Per avere supporto su questo argomento, puoi consultare
la [guida dedicata di WAI](https://docs.italia.it/)
