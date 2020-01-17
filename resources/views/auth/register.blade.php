@extends('layouts.page_bulk')

@section('title', __('Registrazione'))

@section('content')
<p>{{ __('Per completare la registrazione è necessario inserire il tuo indirizzo email di lavoro.') }}</p>
<p>{{ __("Riceverai un messaggio all'indirizzo indicato con le istruzioni per completare la procedura.") }}</p>
<form method="post" action="{{ route('auth.register') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mt-5">
        <div class="form-row">
            <div class="form-group has-form-text col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                    </div>
                    <label for="email">{{ __('Indirizzo email di lavoro') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email') }}" maxlength="255" aria-describedby="email-input-help" aria-required="true" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.email', ['attribute' => __('validation.attributes.email')]) }}</div>
                    @enderror
                </div>
                <small id="email-input-help" class="form-text text-muted">{{ __('es. nome.cognome@agid.gov.it') }}</small>
            </div>
        </div>
        <div class="form-row">
            <div id="tos" class="callout callout-more note">
                <div class="callout-title">
                    <svg class="icon icon-primary"><use xlink:href="{{ asset('svg/sprite.svg#it-clip') }}"></use></svg>
                    <span>{{ __('condizioni del servizio') }}</span>
                </div>
                @excerpt($tos)
                <div class="collapse-div">
                    <div class="collapse-header" id="show-more">
                        <button type="button" class="callout-more-toggle" data-toggle="collapse" data-target="#collapseTos" aria-expanded="false" aria-controls="collapseTos">
                            {{ __('Leggi tutto') }} <span></span>
                        </button>
                    </div>
                    <div id="collapseTos" class="collapse" role="tabpanel" aria-labelledby="show-more">
                        <div class="collapse-body">
                            @remainder($tos)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-check">
                <input class="form-control form-check-input" class="{{ $errors->has('accept_terms') ? ' is-invalid' : '' }}" type="checkbox" id="accept_terms" name="accept_terms" aria-required="true" required>
                <label class="form-check-label" for="accept_terms">{{ __('Accetto le condizioni del servizio') }}</label>
                <div class="invalid-feedback">{{ __('validation.accepted', ['attribute' => __('validation.attributes.accept_terms')]) }}</div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group mb-0 col text-center">
                <button type="submit" class="btn btn-primary">{{ __('Registrati') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
