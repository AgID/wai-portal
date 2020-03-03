Ciao {{ $user->name }},

ti informiamo che il campo *URL* del sito **{{ $website->name }}** della tua pubblica
amministrazione **{{ $website->publicAdministration->name }}**,
è stato modificato su [{{ config('app.name') }}]({{ url('/') }}).

Puoi visualizzare la modifica accedendo alla
[pagina gestione sito]({{ route('websites.show', ['website' => $website]) }}).
