@component('mail::message')
# Invito su {{ config('app.name') }}

Ciao,
sei stato invitato come utente {{ __('auth.roles.'.$user->roles()->first()->name) }}
per la tua PA ({{ current_public_administration()->name }}) su {{ config('app.name') }}!

Per completare la tua iscrizione è necessario effettuare l'accesso con SPID
e la conferma del tuo indirizzo email.

@component('mail::button', ['url' => $signedUrl])
    Accedi e conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi fare copia e incolla di questo link nella barra degli indirizzi del tuo
browser: `{!! $signedUrl !!}`

**È possibile effettuare la conferma dell'indirizzo email entro un'ora
dall'invio di questo messaggio.**
@endcomponent

{{-- //TODO: put message in lang file --}}
