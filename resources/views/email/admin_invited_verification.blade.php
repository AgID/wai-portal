@component('mail::message')
# Invito su {{ config('app.name') }}

Ciao,
sei stato invitato come utente {{ __('auth.roles.'.$user->roles()->first()->name) }}
su {{ config('app.name') }}!

Per completare la tua iscrizione è necessaria la conferma del tuo indirizzo email.

@component('mail::button', ['url' => route('admin-do_verify', ['email' => urlencode($user->email), 'token' => $token])])
    Conferma indirizzo email
@endcomponent

Se non riesci a confermare cliccando sul bottone,
puoi visitare la pagina [{{ url('/admin/user/verify') }}]({{ url('/admin/user/verify') }})
ed inserire il tuo indirizzo email ed il seguente codice di verifica:
`{!! $token !!}`
@endcomponent

{{-- //TODO: put message in lang file --}}
