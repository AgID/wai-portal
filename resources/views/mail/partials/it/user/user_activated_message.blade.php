Ciao {{ $user->name }},

ti informiamo che il nuovo utente {{ $activatedUser->full_name }} è stato correttamente
attivato per la tua pubblica amministrazione.

Da questo momento in poi potrai gestirlo accedendo al portale [{{ config('app.name') }}]({{ url('/') }})
dalla [sezione dedicata alla gestione utenti]({{ url(route('users.index')) }}).
