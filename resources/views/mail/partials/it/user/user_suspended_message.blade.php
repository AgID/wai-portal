Ciao {{ $user->name }},

l'utente **{{ $suspendedUser->full_name }}** è stato correttamente sospeso.

Puoi riattivarlo in qualsiasi momento dalla
[pagina gestione utenti]({{ url(route('users.index')) }}).
