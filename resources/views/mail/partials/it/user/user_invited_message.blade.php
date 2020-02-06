Ciao {{ $user->name }},

abbiamo inviato un invito a **{{ $invitedUser->full_name }}** per la tua
pubblica amministrazione **{{ $publicAdministration->name }}** su
[{{ config('app.name') }}]({{ url('/') }}).

Riceverai una notifica nel momento in cui l'utente
avrà completato la procedura di attivazione del profilo.
