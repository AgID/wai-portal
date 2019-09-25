@component('mail::message')
# Account creato

Ciao {{ $user->full_name }},

grazie per esserti registrato su {{ config('app.name') }}!

Per completare la tua iscrizione è necessaria la
conferma del tuo indirizzo email.

@component('mail::button', ['link' => $signedUrl])
    Conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi fare copia e incolla di questo link nella barra degli indirizzi del tuo
browser: `{!! $signedUrl !!}`

**È possibile effettuare la conferma dell'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dall'invio di questo
messaggio.**
@endcomponent
