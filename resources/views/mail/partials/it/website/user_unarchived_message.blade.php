Ciao {{ $user->name }},

ti informiamo che il sito **{{ $website->name }}** della pubblica amministrazione
**{{ $website->publicAdministration->name }}**, è stato correttamente riattivato su
[{{ config('app.name') }}]({{ url('/') }}).
