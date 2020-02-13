Ciao {{ $publicAdministration->rtd_name ?? '' }}

in qualità di *Responsabile per la transizione al digitale* ti informiamo che
la tua Pubblica Amministrazione **{{ $publicAdministration->name }}** è
stata registrata con successo su [{{ config('app.name') }}]({{ url('/') }})
da *{{ $registeringUser->full_name }}* che, all'attivazione del sito,
diventerà utente amministratore della tua PA su {{ config('app.name_short') }}.
