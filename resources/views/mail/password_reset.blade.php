@component('mail::message')
# Reset della password

Ciao {{ $user->name }} {{ $user->familyName }},
ci è pervenuta una richiesta di reset della tua password su {{ config('app.name') }}.

Se sei stato tu a fare la richiesta perché non riesci ad accedere,
usa il bottone per andare alla pagina di reset della password.

@component('mail::button', ['url' => route('admin.password.reset.show', $token)])
    Reimposta password
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi visitare la pagina [{{ url(route('admin.password.reset.show')) }}]({{ url(route('admin.password.reset.show')) }})
ed inserire il seguente codice:
`{!! $token !!}`

**È possibile effettuare il reset della password entro un'ora dalla richiesta
(fino alle {{ $user->passwordResetToken->created_at->addHour()->format('H:i:s') }} del {{ $user->passwordResetToken->created_at->addHour()->format('d/m/Y') }})**

**Se non sei stato tu a richiedere il reset la password, puoi ignorare questo messaggio email.**
@endcomponent

{{-- //TODO: put message in lang file --}}
