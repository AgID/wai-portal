Ciao {{ $user->name }},

ti informiamo che la nuova utenza di **{{ $activatedUser->full_name }}** è stata
correttamente attivata per la tua pubblica amministrazione.

Da questo momento in poi potrai gestirla dalla
[pagina gestione utenti]({{ url(route('users.index')) }}).
