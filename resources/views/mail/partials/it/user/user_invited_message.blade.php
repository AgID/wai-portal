Ciao {{ $user->name }},

un invito a [{{ config('app.name') }}]({{ url('/') }}) è stato
correttamente inviato all’utente {{ $invitedUser->full_name }}
in relazione alla tua pubblica amministrazione ({{ $publicAdministration->name }}).

Riceverai una notifica nel momento in cui l’utente
avrà completato la procedura di attivazione del profilo.
