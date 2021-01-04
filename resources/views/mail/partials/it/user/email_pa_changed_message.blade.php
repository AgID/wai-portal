Ciao {{ $user->full_name }},

è stato modificato l'indirizzo email associato alla tua pubblica amministrazione **{{ $publicAdministration->name }}** su [{{ config('app.name') }}]({{ url('/') }}).

L'indirizzo aggiornato è {{ $updatedEmail }}.
