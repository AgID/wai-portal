<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($user)
    @method('put')
    <div class="alert alert-warning rounded" role="alert">
        {{ __("L'indirizzo email è una crendenziale ed è usato per il recupero della password.") }}
        <br>
        {{ __('Dal prossimo accesso dovrai utilizzare il nuovo eventuale indirizzo email.') }}
    </div>
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h5 class="section-header">{{ __('anagrafica') }}</h5>
    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                </div>
                <label for="name">{{ __('Nome') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" maxlength="50" aria-required="true" required>
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
                <input type="text" class="form-control" id="family_name" name="family_name" value="{{ old('family_name', $user->family_name ?? '') }}" maxlength="50" aria-required="true" required>
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
                <label for="email">{{ __('Indirizzo email di lavoro') }}</label>
                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" maxlength="75" aria-describedby="email-input-help" aria-required="true" required>
                @error('email')
                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.email', ['attribute' => __('validation.attributes.email')]) }}</div>
                @enderror
            </div>
            <small id="email-input-help" class="form-text text-muted">{{ __('es. nome.cognome@agid.gov.it') }}</small>
        </div>
    </div>
    <div class="form-row mt-4">
        <div class="form-group mb-0 col text-center">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
        </div>
    </div>
    @endcomponent
</form>
