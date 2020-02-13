@extends('layouts.page')

@section('title', __('Cambio password'))

@section('content')
@component('layouts.components.box')
<form method="post" action="{{ route('admin.password.change') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mt-5">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="password">{{ __('Nuova password') }}</label>
                <input type="password" class="form-control input-password input-password-strength-meter{{ $errors->has('password') ? ' is-invalid' : '' }}" data-enter-pass="{{ __('Inserisci almeno 8 caratteri tra maiuscole, minuscole, numeri e simboli') }}" id="password" name="password" maxlength="50" aria-required="true" required>
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
                <input type="password" class="form-control input-password{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" id="password_confirmation" name="password_confirmation" maxlength="50" aria-required="true" required>
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
                <button type="submit" class="btn btn-primary">{{ __('Cambia password') }}</button>
            </div>
        </div>
    </div>
</form>
@endcomponent
@endsection
