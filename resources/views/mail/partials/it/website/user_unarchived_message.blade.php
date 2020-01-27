Ciao {{ $user->name }},

ti informiamo che il sito "{{ $website->name }}" registrato su [{{ config('app.name') }}]({{ url('/') }})
e relativo alla pubblica amministrazione ({{ $website->publicAdministration->name }}),
è stato correttamente riattivato.
