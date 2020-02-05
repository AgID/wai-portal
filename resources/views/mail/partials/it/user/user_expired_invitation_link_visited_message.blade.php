Ciao {{ $user->name }},

ti informiamo che l'utente **{{ $invitedUser->full_name }}** ha cercato di attivare
il suo invito al portale [{{ config('app.name') }}]({{ url('/') }}),
ma il link è scaduto.

Se lo desideri, puoi inviare uno nuovo link di attivazione dal suo
[profilo]({{ url(route('users.show', ['user' => $this->invitedUser])) }}).
