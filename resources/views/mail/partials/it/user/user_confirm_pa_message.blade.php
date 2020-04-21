Ciao,

sei stato invitato/a per la tua pubblica amministrazione
**{{ $publicAdministration->name }}** su [{{ config('app.name') }}]({{ url('/') }}).

Per confermare devi cliccare sul link.

Cliccando sul bottone (o visitando il link riportato sotto) **dichiari di
aver letto e accettato i [termini del servizio]({{ route('legal-notes') }}#tos)**
di {{ config('app.name') }}.

@component('mail::button', ['link' => $verificationUrl])
    Accetta invito
@endcomponent

Se non riesci a confermare cliccando sul bottone,
copia e incolla questo link nella barra degli indirizzi del
browser: `{!! $verificationUrl !!}`

**Puoi confermare l'indirizzo email
entro {{ config('auth.verification.expire', 7) }} giorni dalla ricezione di questo
messaggio.**
