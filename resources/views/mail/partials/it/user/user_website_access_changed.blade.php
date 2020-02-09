Ciao {{ $user->name }},

ti informiamo che i permessi dell'utente **{{ $modifiedUser->full_name }}** sono
stati modificati.

Puoi verificare i nuovi permessi dal suo
[profilo]({{ url(route('users.show', ['user' => $this->invitedUser])) }}).
