Ciao {{ $user->name }},

sei stato invitato/a per la tua pubblica amministrazione
**{{ $publicAdministration->name }}** su [{{ config('app.name') }}]({{ url('/') }}).

Puoi accedere e confermare l'invito dal pannello di gestione amministrazioni.
