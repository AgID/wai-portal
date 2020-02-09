Ciao {{ $user->name }},

sei stato invitato/a su [{{ config('app.name') }}]({{ url('/') }}) come
**super amministratore**.

Per completare la tua iscrizione, conferma il tuo indirizzo email.

@component('mail::button', ['link' => $signedUrl])
    Conferma email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
copia e incolla questo link nella barra degli indirizzi del
browser: `{!! $signedUrl !!}`

**Puoi confermare il tuo indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dalla ricezione di questo
messaggio.**
