Ciao {{ $user->name }},

il sito web "{{ $website->name }}" è stato
aggiunto con successo su [{{ config('app.name') }}]({{ url('/') }}).
