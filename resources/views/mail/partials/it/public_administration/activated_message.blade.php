Ciao {{ $user->name }},

ti informiamo che la pubblica amministrazione ({{ $publicAdministration->name }})
è stata correttamente attivata su [{{ config('app.name') }}]({{ url('/') }}).

Da questo momento in poi i dati di traffico relativi al sito istituzionale
dell'amministrazione saranno raccolti ed elaborati dalla piattaforma.

Se hai bisogno di supporto su questo o altri argomenti relativi a {{ config('app.name_short') }}, puoi consultare
la [guida d’uso dedicata](https://docs.italia.it/)
