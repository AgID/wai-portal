@extends('layouts.page_bulk')

@section('title', __(':link scaduto', ['link' => ($invitation ? 'Invito' : 'Link di conferma')]));

@section('title-after')
    <svg class="icon icon-xl icon-danger mb-1 ml-2"><use xlink:href="{{ asset('svg/sprite.svg#it-error') }}"></use></svg>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-8">
            <p class="lead text-primary font-weight-semibold">{{ __(':link inserito è scaduto e non può più essere usato.', ['link' => ( $invitation ? "L'invito" : 'Il link di conferma') ]) }}</p>
            <p>{{ __('Per proseguire è necessario generarne uno nuovo.') }}</p>
        </div>
        <div class="col-sm-4 d-flex align-items-center justify-content-center">
            <img src="{{ asset('images/verification-email-sent.svg') }}" alt="">
        </div>
        <div class="col">
            <p class="font-italic mt-4 mb-0">
                <small>
                    @if($invitation)
                    {{ __("Contatta un amministratore per richiedere l'invio di un nuovo invito") }}
                    @else
                    {{ __('Puoi richiedere autonomamente la ') }}
                    <a href="{{ route('verification.resend', [], false) }}">{{ __('spedizione di una nuova mail di verifica') }}</a>.
                    @endif
                </small>
            </p>
        </div>
    </div>
@endsection
