Ciao,

sei stato invitato/a per la tua PA ({{ $publicAdministration->name }}) su
[{{ config('app.name') }}]({{ url('/') }}).

Per completare la tua iscrizione devi effettuare l'accesso con SPID
e confermare il tuo indirizzo email.

@component('mail::button', ['link' => $signedUrl])
    Accedi e conferma email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
copia e incolla questo link nella barra degli indirizzi del
browser: `{!! $signedUrl !!}`

**Puoi confermare l'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dalla ricezione di questo
messaggio.**
