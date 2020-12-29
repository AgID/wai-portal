Ciao {{ $user->full_name }},

hai inserito un nuovo indirizzo email su [{{ config('app.name') }}]({{ url('/') }}).

Per completare la procedura è necessario procedere alla verifica.

@component('mail::button', ['link' => $signedUrl])
    Verifica email
@endcomponent

Se non riesci a completare la verifica cliccando sul bottone,
copia e incolla questo link nella barra degli indirizzi del
browser: `{!! $signedUrl !!}`

**Puoi verificare l'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dalla ricezione di questo
messaggio.**

*Se pensi di aver ricevuto questo messaggio per errore puoi scrivere a
{{ config('site.owner.mail') }}.*
