@extends('layouts.page_bulk')

@section('title', __('Password dimenticata?'))

@section('content')
<p>{{ __('Se non riesci ad accedere al tuo account amministrativo, puoi richiedere un reset della password inserendo qui il tuo indirizzo email.') }}</p>
<form method="post" action="{{ route('admin.password.reset.send') }}" class="needs-validation" novalidate>
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
        <div class="form-row">
            <div class="form-group mb-0 col text-center">
                <button type="submit" class="btn btn-primary">{{ __('Reset password') }}</button>
            </div>
        </div>
    </div>
</form>
@endsection
