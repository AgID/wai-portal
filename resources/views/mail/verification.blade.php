@component('mail::message')
# Account creato

Ciao {{ $user->name }} {{ $user->family_name }},
grazie per esserti registrato su {{ config('app.name') }}!

Per completare la tua iscrizione è necessaria la
conferma del tuo indirizzo email.

@component('mail::button', ['url' => $signedUrl])
    Conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi fare copia e incolla di questo link nella barra degli indirizzi del tuo
browser: `{!! $signedUrl !!}`
@endcomponent

{{-- //TODO: put message in lang file --}}
