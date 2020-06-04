Ciao {{ $user->full_name }},

ti sei registrato oppure sei stato invitato su [{{ config('app.name') }}]({{ url('/') }}).

Per completare la procedura conferma il tuo indirizzo email.

@component('mail::button', ['link' => $signedUrl])
    Conferma email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
copia e incolla questo link nella barra degli indirizzi del
browser: `{!! $signedUrl !!}`

**Puoi confermare l'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dalla ricezione di questo
messaggio.**

*Se pensi di aver ricevuto questo messaggio per errore puoi scrivere a
{{ config('site.owner.mail') }}.*
