Ciao {{ $user->name }},

ti informiamo che la URL del sito "{{ $website->name }}" registrato
su [{{ config('app.name') }}]({{ url('/') }}) e relativo
alla pubblica amministrazione ({{ $website->publicAdministration->name }}),
è stata modificata.

Puoi visualizzare la modifica accedendo alla [sezione dedicata alla gestione siti]({{ url(route('websites.index')) }}).
