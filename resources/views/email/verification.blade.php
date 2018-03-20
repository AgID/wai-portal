@component('mail::message')
  # Account creato

  Ciao {{ $user->name }} {{ $user->familyName }},
  grazie per esserti registrato su {{ config('app.name') }}!

  Per completare la tua iscrizione è necessaria la conferma del tuo indirizzo
  email.

  @component('mail::button', ['url' => route('auth-do_verify', $user->verificationToken)])
  Conferma indirizzo email
  @endcomponent
@endcomponent

{{-- //TODO: put message in lang file --}}
