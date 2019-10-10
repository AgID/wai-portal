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
        <p>{!! __('Uno degli amministratori ti ha inviato un invito al tuo indirizzo :email.', ['email' => '<strong>' . e($user->email) . '</strong>']) !!}</p>
        <p>{{ __('Per procedere clicca sul link che trovi nella email.') }}</p>
    </div>
    <div class="col-sm-4 d-flex align-items-center justify-content-center">
        <img src="{{ asset('images/verification-email-sent.svg') }}" alt="">
    </div>
    <div class="col">
        <p class="font-italic mt-4 mb-0">
            <small>
                {{ __('Se non hai ricevuto il link al tuo indirizzo, controlla che la casella non sia piena e verifica che il messaggio non sia stato erroneamente classificato come spam.') }}
            </small>
        </p>
        <p class="font-italic mb-0">
            <small>
                {{ __('Se necessario, contatta un amministratore per riceverne un altro') }}.
            </small>
        </p>
    </div>
</div>
@endsection
