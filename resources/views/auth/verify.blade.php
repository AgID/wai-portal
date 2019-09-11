@extends('layouts.page_bulk')

@section('before-title')
<svg class="icon icon-xl icon-primary mb-2"><use xlink:href="{{ asset('svg/sprite.svg#it-clock') }}"></use></svg>
@endsection

@section('title', __('In attesa di conferma'))

@section('page-content')
<div class="page-overlay"></div>
@parent
@endsection

@section('content')
<div class="row">
    <div class="col-sm-8">
        <p>{!! __('Abbiamo inviato un link di conferma al tuo indirizzo <strong>:email</strong>.', ['email' => e($user->email)]) !!}</p>
        <p>{{ __('Per procedere clicca sul link che ti abbiamo inviato.') }}</p>
        <p>
            <a class="btn btn-primary" role="button" href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.verification.resend' : 'verification.resend', [], false) }}">
                {{ __('Rispedisci mail di verifica') }}
            </a>
            <a class="btn btn-primary" role="button" href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.user.profile.edit' : 'user.profile.edit', [], false) }}">
                {{ ('Modifica indirizzo mail') }}
            </a>
        </p>
        <p>
            <span>{{ __('oppure') }}</span>
            <a href="{{ route('home') }}">{{ __('torna alla pagina iniziale') }}</a>.
        </p>
    </div>
    <div class="col-sm-4 d-flex align-items-center justify-content-center">
        <img src="https://placeholder.pics/svg/180">
    </div>
</div>
@endsection
