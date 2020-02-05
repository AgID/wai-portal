Ciao {{ $user->name }},

ti informiamo che il sito **{{ $website->name }}** è stato
correttamente attivato e da questo momento potrai gestirlo dalla
[pagina gestione siti]({{ route('websites.index') }}).
