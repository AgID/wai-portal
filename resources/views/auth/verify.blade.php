@extends('layouts.page_bulk')

@section('before-title')
<svg class="icon icon-xl icon-primary mb-2"><use xlink:href="{{ asset('svg/sprite.svg#it-clock') }}"></use></svg>
@endsection

@section('title', __('In attesa di conferma'))

@section('content')
<div class="row">
    <div class="col-sm-8">
        <p>{{ __('Ti sei registrato oppure sei stato invitato su :app.', ['app' => config('app.name')]) }}</p>
        <p>{!! __('Per andare avanti è necessaria la verifica del tuo indirizzo :email.', ['email' => '<strong>' . e($user->email) . '</strong>']) !!}</p>
        <p>{{ __('Clicca sul link che trovi nel messaggio che ti abbiamo inviato.') }}</p>
    </div>
    <div class="col-sm-4 d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/verification-email-sent.svg') }}" alt="">
    </div>
    <div class="col">
        <p class="font-italic mt-4 mb-1">
            <small>
                {{ __('Se non hai ricevuto il link al tuo indirizzo, controlla che la casella non sia piena e verifica che il messaggio non sia stato erroneamente classificato come spam.') }}
                {{ __('Se necessario, ') }}
                <a href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.verification.resend' : 'verification.resend', [], false) }}">{{ __('invia una nuova mail di verifica') }}</a>.
            </small>
        </p>
        <p class="font-italic mb-0">
            <small>
                {{ __("Se l'indirizzo email è errato, ") }}
                <a href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.user.profile.edit' : 'user.profile.edit', [], false) }}">{{ ('puoi modificarlo') }}</a>.
            </small>
        </p>
    </div>
</div>
@endsection
