<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($user)
    @method('put')
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3 class="section-header">{{ __('anagrafica') }}</h3>
    @isset($user)
    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                </div>
                <label for="name">{{ __('Nome') }}</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" aria-required="true" required readonly>
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
                <input type="text" class="form-control" id="family_name" name="family_name" value="{{ $user->family_name }}" aria-required="true" required readonly>
            </div>
        </div>
    </div>
    @endisset
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
    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-card') }}"></use></svg></div>
                </div>
                <label for="fiscal_number">{{ __('Codice fiscale') }}</label>
                <input type="text" class="form-control{{ $errors->has('fiscal_number') ? ' is-invalid' : '' }}" id="fiscal_number" name="fiscal_number" value="{{ old('fiscal_number', $user->fiscal_number ?? '') }}" maxlength="16" aria-required="true" required {{ (optional($user->status ?? null)->is(UserStatus::INVITED) ?? true) ? '' : 'readonly' }}>
                @error('fiscal_number')
                <div class="invalid-feedback">{{ $errors->first('fiscal_number') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.fiscal_number')]) }}</div>
                @enderror
            </div>
        </div>
    </div>
    <h3 class="section-header{{ $errors->has('permissions') ? ' is-invalid' : '' }}">{{ __('Permessi sui siti web') }}</h3>
    @error('permissions')
    <div class="invalid-feedback">{{ $errors->first('permissions') }}</div>
    @enderror
    <h6>{{ __("Puoi assegnare all'utente un ruolo di amministratore") }}</h6>
    <div class="form-row justify-content-between">
        <div class="form-check d-flex align-items-center col-md-5 mb-5 mt-0">
            <div class="toggles w-100 px-3 py-2 lightgrey-bg-c1">
                <label class="mb-0" for="is_admin">
                    {{ __('Utente con ruolo di amministratore') }}
                    <input class="{{ $errors->has('is_admin') ? 'is-invalid' : '' }}" type="checkbox" id="is_admin" name="is_admin" value="1" aria-describedby="is_admin-help" {{ old('is_admin', !session()->hasOldInput() && ($isAdmin ?? false)) ? 'checked' : '' }} disabled>
                    <span class="lever"></span>
                    @error('is_admin')
                    <div class="invalid-feedback">{{ $errors->first('is_admin') }}</div>
                    @enderror
                </label>
            </div>
        </div>
        <div id="is_admin-help"class="form-check col-md-6 mb-5 mt-0">
            <div class="callout callout-highlight note m-0">
                <p>{{ UserRole::getLongDescription(UserRole::ADMIN) }}</p>
                <p class="font-italic"><small>{{ __('Il ruolo di amministratore attiva automaticamente i permessi di lettura e scrittura per tutti i siti web.') }}</small></p>
            </div>
        </div>
    </div>
    <h6>{{ __('Puoi assegnare permessi diversificati per ciascun sito') }}</h6>
    <p><small>
        {{ UserPermission::getLongDescription(UserPermission::READ_ANALYTICS) }}<br>
        {{ UserPermission::getLongDescription(UserPermission::MANAGE_ANALYTICS) }}
    </small></p>
    <div class="form-row">
        @include('partials.datatable')
    </div>
    <div class="form-row mt-4">
        <div class="form-group mb-0 col text-center">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
        </div>
    </div>
    @endcomponent
</form>
