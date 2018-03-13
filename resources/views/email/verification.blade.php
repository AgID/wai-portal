@component('mail::message')
# Account creato

Ciao {{ $user->name }} {{ $user->familyName }},
grazie per esserti registrato su Analytics Italia!

Per completare la tua iscrizione è necessaria la conferma del tuo indirizzo
email.

@component('mail::button', ['url' => route('auth-do_verify', $user->verificationToken)])
Conferma indirizzo email
@endcomponent

Grazie,<br>
Il team di {{ config('app.name') }}
@endcomponent

{{-- //TODO: put message in lang file --}}