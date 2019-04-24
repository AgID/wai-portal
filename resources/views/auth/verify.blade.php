@extends('layouts.page')

@section('title', __('ui.pages.auth-verify.title'))

@section('page-content')
{{-- //TODO: allow to change email address --}}
    <p>Prima di continuare è necessario verificare la correttezza del tuo indirizzo email {{ $user->email }}.</p>
    <p class="u-margin-bottom-xl">Clicca sul link che abbiamo inviato alla tua casella di posta.{{-- //TODO: put message in lang file --}}</p>
    <a role="button" href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.verification.resend' : 'verification.resend', [], false) }}" class="Button Button--default u-text-xs submit">
        Rispedisci mail di verifica{{-- //TODO: put message in lang file --}}
    </a>
@endsection
