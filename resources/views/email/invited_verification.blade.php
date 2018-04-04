@component('mail::message')
# Invito su {{ config('app.name') }}

Ciao,
sei stato invitato come utente {{ __('auth.roles.'.$user->roles()->first()->name) }}
per la tua PA ({{ $user->publicAdministration->name }}) su {{ config('app.name') }}!

Per completare la tua iscrizione è necessario effettuare l'accesso con SPID
e la conferma del tuo indirizzo email.

@component('mail::button', ['url' => route('auth-do_verify', $token)])
    Accedi e conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi visitare la pagina [{{ url('/user/verify') }}]({{ url('/user/verify') }})
ed inserire il seguente codice di verifica:
`{!! $token !!}`
@endcomponent

{{-- //TODO: put message in lang file --}}
