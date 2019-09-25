@extends('layouts.page')

@section('title', __('Profilo utente'))

@section('content')
@component('layouts.components.box')
<form method="post" action="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.user.profile.update' : 'user.profile.update') }}" class="needs-validation" novalidate>
    @method('patch')
    @csrf
    @if ($user->isA(UserRole::SUPER_ADMIN))
    <div class="alert alert-warning rounded" role="alert">
        {{ __("L'indirizzo email è una crendenziale ed è usato per il recupero della password.") }}
        <br>
        {{ __('Dal prossimo accesso dovrà essere utilizzato il nuovo eventuale indirizzo email.') }}
    </div>
    @endif
    <div class="mt-5">
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                    </div>
                    <label for="name">{{ __('Nome') }}</label>
                    <input type="text" class="form-control{{ $user->isA(UserRole::SUPER_ADMIN) ? '' : ' is-disabled' }}{{ $errors->has('name') ? ' is-invalid' : '' }}" id="name" name="name" value="{{ $user->name }}" maxlength="255" required {{ $user->isA(UserRole::SUPER_ADMIN) ? '' : 'readonly' }}>
                    @error('name')
                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.name')]) }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                    </div>
                    <label for="family_name">{{ __('Cognome') }}</label>
                    <input type="text" class="form-control{{ $user->isA(UserRole::SUPER_ADMIN) ? '' : ' is-disabled' }}{{ $errors->has('family_name') ? ' is-invalid' : '' }}" id="family_name" name="family_name" value="{{ $user->family_name }}" maxlength="255" required {{ $user->isA(UserRole::SUPER_ADMIN) ? '' : 'readonly' }}>
                    @error('family_name')
                    <div class="invalid-feedback">{{ $errors->first('family_name') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.family_name')]) }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group has-form-text col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                    </div>
                    <label for="email">{{ __('Indirizzo email istituzionale') }}</label>
                    <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email') ?? $user->email }}" maxlength="255" aria-labelledby="email-input-help" aria-required="true" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.email', ['attribute' => __('validation.attributes.email')]) }}</div>
                    @enderror
                </div>
                <small id="email-input-help" class="form-text text-muted">{{ __('Indirizzo email di lavoro (es. nome.cognome@agid.gov.it).') }}</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group mb-0 col text-center">
                <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
            </div>
        </div>
    </div>
</form>
@endcomponent
@endsection
