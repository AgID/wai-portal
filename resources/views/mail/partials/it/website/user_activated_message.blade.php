Ciao {{ $user->name }},

ti informiamo che il tuo sito "{{ $website->name }}" è stato
correttamente attivato e da questo momento potrai gestirlo accedendo al
portale [{{ config('app.name') }}]({{ url('/') }}).
