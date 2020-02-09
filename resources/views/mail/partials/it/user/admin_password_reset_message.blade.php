Ciao {{ $user->name }},

ci è pervenuta una richiesta di reset della tua password su
[{{ config('app.name') }}]({{ url('/') }}).

Se sei stato/a tu a fare la richiesta perché non riesci ad accedere,
usa il bottone per andare alla pagina di reset della password.

@component('mail::button', ['link' => route('admin.password.reset.show', $token)])
    Reimposta password
@endcomponent

Se non riesci a usare il bottone,
visita la pagina [{{ url(route('admin.password.reset.show')) }}]({{ url(route('admin.password.reset.show')) }})
e inserisci il seguente codice:
`{!! $token !!}`

**Puoi completare il reset della password entro un'ora dalla richiesta
(fino alle {{ $user->passwordResetToken->created_at->addHour()->format('H:i:s') }} del
{{ $user->passwordResetToken->created_at->addHour()->format('d/m/Y') }})**

**Se non sei stato/a tu a richiedere il reset della password, ignora questa email.**
