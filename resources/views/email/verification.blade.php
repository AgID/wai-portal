@component('mail::message')
    # Account creato

    Ciao {{ $user->name }} {{ $user->familyName }},
    grazie per esserti registrato su {{ config('app.name') }}!

    Per completare la tua iscrizione è necessaria la
    conferma del tuo indirizzo email.

    @component('mail::button', ['url' => route('auth-do_verify', $token)])
        Conferma indirizzo email
    @endcomponent

    Se non riesci a confermare cliccando sul bottone,
    puoi visitare la pagina [{{ url('/verify') }}]({{ url('/verify') }})
    ed inserire il seguente codice di verifica:
    `{!! $token !!}`
@endcomponent

{{-- //TODO: put message in lang file --}}
