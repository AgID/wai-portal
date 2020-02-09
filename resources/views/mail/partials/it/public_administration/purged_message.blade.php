Ciao {{ $user->name }},

ti informiamo che la pubblica amministrazione **{{ $publicAdministration->name }}**
è stata eliminata da [{{ config('app.name') }}]({{ url('/') }}) perché non
abbiamo ricevuto dati entro {{ config('wai.purge_expiry') }} giorni
dalla registrazione.

Da questo momento in poi non sarà più possibile usare il codice di tracciamento
relativo al sito istituzionale.

Se intendi ancora tracciare i dati di traffico web della tua pubblica
amministrazione con [{{ config('app.name_short') }}]({{ url('/') }}), dovrai
effettuare una nuova registrazione.
