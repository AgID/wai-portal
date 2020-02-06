Ciao {{ $publicAdministration->rtd_name ?? '' }}

in qualità di *Responsabile per la transizione al digitale* ti informiamo che
il sito **{{ $website->name }}** della tua Pubblica Amministrazione
**{{ $publicAdministration->name }}** è è stato correttamente attivato su
[{{ config('app.name') }}]({{ url('/') }}).
