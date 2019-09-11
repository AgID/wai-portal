@extends('layouts.page_bulk')

@section('title', __('Reset della password'))

@section('content')
<form method="post" action="{{ route('admin.password.reset') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mt-5">
        <div class="form-row">
            <div class="form-group has-form-text col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                    </div>
                    <label for="email">{{ __('Indirizzo email') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('inserisci il tuo indirizzo email') }}" aria-labelledby="email-input-help" aria-required="true" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.email', ['attribute' => __('validation.attributes.email')]) }}</div>
                    @enderror
                </div>
                <small id="email-input-help" class="form-text text-muted">{{ __("L'indirizzo email con il quale sei registrato.") }}</small>
            </div>
        </div>
        @isset($token)
        <input type="hidden" id="token" name="token" value="{{ $token }}">
        @else
        <div class="form-row">
            <div class="form-group has-form-text col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                    </div>
                    <label for="token">{{ __('Codice di reset') }}</label>
                    <input type="text" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}" id="token" name="token" value="{{ old('token') }}" placeholder="{{ __('inserisci codice che hai ricevuto via email') }}" aria-labelledby="token-input-help" aria-required="true" required>
                    @error('token')
                    <div class="invalid-feedback">{{ $errors->first('token') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.token')]) }}</div>
                    @enderror
                </div>
                <small id="token-input-help" class="form-text text-muted">{{ __('Il codice di reset che hai ricevuto via email.') }}</small>
            </div>
        </div>
        @endif
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="password">{{ __('Nuova password') }}</label>
                <input type="password" class="form-control input-password input-password-strength-meter{{ $errors->has('password') ? ' is-invalid' : '' }}" id="password" name="password" placeholder="{{ __('inserisci la tua nuova password') }}" aria-required="true" required>
                <span class="password-icon" aria-hidden="true">
                    <svg class="password-icon-visible icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-password-visible') }}"></use></svg>
                    <svg class="password-icon-invisible icon icon-sm d-none"><use xlink:href="{{ asset('svg/sprite.svg#it-password-invisible') }}"></use></svg>
                </span>
                @error('password')
                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.password')]) }}</div>
                @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="password_confirmation">{{ __('Conferma nuova password') }}</label>
                <input type="password" class="form-control input-password{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="password_confirmation" name="password_confirmation" placeholder="{{ __('conferma la tua nuova password') }}" aria-required="true" required>
                <span class="password-icon" aria-hidden="true">
                    <svg class="password-icon-visible icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-password-visible') }}"></use></svg>
                    <svg class="password-icon-invisible icon icon-sm d-none"><use xlink:href="{{ asset('svg/sprite.svg#it-password-invisible') }}"></use></svg>
                </span>
                @error('password_confirmation')
                <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.password_confirmation')]) }}</div>
                @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group mb-0 col text-center">
                <button type="submit" class="btn btn-primary">{{ __('Reimposta password') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
