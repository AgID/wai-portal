@component('mail::message')
# Invito su {{ config('app.name') }}

Ciao,
sei stato invitato/a per la tua PA ({{ $publicAdministration->name }}) su
{{ config('app.name') }}.

Per completare la tua iscrizione è necessario effettuare l'accesso con SPID
e la conferma del tuo indirizzo email.

@component('mail::button', ['link' => $signedUrl])
    Accedi e conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi fare copia e incolla di questo link nella barra degli indirizzi del tuo
browser: `{!! $signedUrl !!}`

**È possibile effettuare la conferma dell'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dall'invio di questo
messaggio.**
@endcomponent
