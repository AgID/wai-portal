<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($credential)
    @method('put')
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3 class="section-header">{{__('Informazioni credenziale')}}</h3>
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-help-circle') }}"></use></svg></div>
                </div>
                <label for="credential_name">{{ ucfirst(__('nome della credenziale')) }}</label>
                <input type="text"
                    class="form-control{{ $errors->has('credential_name') ? ' is-invalid' : '' }}"
                    id="credential_name"
                    name="credential_name"
                    value="{{ old('credential_name', $credential->client_name ?? '') }}"
                    maxlength="255"
                    aria-describedby="name-input-help"
                    aria-required="true"
                    required>
                @error('credential_name')
                <div class="invalid-feedback">{{ $errors->first('credential_name') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.credential_name')]) }}</div>
                @enderror
            </div>
            <small id="name-input-help" class="form-text text-muted">{{ __('Scegli un nome per la credenziale.') }}</small>
        </div>
    </div>
    @empty($credential)
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-key') }}"></use></svg></div>
                </div>
                <div class="bootstrap-select-wrapper form-control flex-grow-1{{ $errors->has('type') ? ' is-invalid' : '' }}">
                    <label for="type">{{ ucfirst(__('tipologia')) }}</label>
                    <select title="{{ __('Scegli una tipologia') }}" id="type" name="type" aria-required="true" required>
                        <option value="{{ CredentialType::ADMIN }}" {{ old('type', $type->value ?? '') === CredentialType::ADMIN ? 'selected' : '' }}>
                            {{ ucfirst(CredentialType::getDescription(CredentialType::ADMIN)) }}
                        </option>
                        <option value="{{ CredentialType::ANALYTICS }}" {{ old('type', $type->value ?? '') === CredentialType::ANALYTICS ? 'selected' : '' }}>
                            {{ ucfirst(CredentialType::getDescription(CredentialType::ANALYTICS)) }}
                        </option>
                    </select>
                </div>
                @error('type')
                <div class="invalid-feedback">{{ $errors->first('type') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.type')]) }}</div>
                @enderror
            </div>
            <div id="name-input-help" class="form-text text-muted">
                <div class="alert alert-info" role="alert">
                    <small>
                        {{ CredentialType::getLongDescription(CredentialType::ADMIN) }}<br>
                        {{ CredentialType::getLongDescription(CredentialType::ANALYTICS) }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="box-section lightgrey-bg-a1 my-4 py-5">
        <h5 class="section-header mb-4">{{ __('tipologia') }}</h5>
        <div class="row">
            <div class="col-md-4 d-flex align-items-center">
                <span class="badge user-role {{ strtolower($type->key) }}">
                    {{ strtoupper($type->description) }}
                </span>
            </div>
            <div class="col-md-8">
                <p class="mb-0">
                    <svg class="icon"><use xlink:href="{{ asset('svg/sprite.svg#it-info-circle') }}"></use></svg>
                    {{ CredentialType::getLongDescription($type->value) }}
                </p>
            </div>
        </div>
    </div>
    @endempty
    <h3 class="section-header{{ $errors->has('permissions') ? ' is-invalid' : '' }}">{{ __('Permessi sui siti web') }}</h3>
    @error('permissions')
    <div class="invalid-feedback">{{ $errors->first('permissions') }}</div>
    @enderror
    <div class="row justify-content-between">
        @unless (($type->value ?? '') === CredentialType::ADMIN)
        <div class="col">
            <div class="card-wrapper">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">
                        {!! __('Per le credenziali di tipologia :analytics_credential, puoi assegnare permessi diversificati per ciascun sito.', [
                            'analytics_credential' => '<em>' . CredentialType::getDescription(CredentialType::ANALYTICS) . '</em>'
                        ]) !!}
                    </h5>
                    <p class="card-text">
                        <ul>
                            <li>{{ CredentialPermission::getLongDescription(CredentialPermission::READ) }}</li>
                            <li>{{ CredentialPermission::getLongDescription(CredentialPermission::WRITE) }}</li>
                        </ul>
                    </p>
                  </div>
                </div>
              </div>
        </div>
        @endunless
        @unless (($type->value ?? '') === CredentialType::ANALYTICS)
        <div class="col {{ empty($credential) ? 'border-left' : '' }}">
            <div class="card-wrapper">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">
                        {!! __('Le credenziali di tipologia :admin_credential hanno permessi completi su tutti i siti.', [
                            'admin_credential' => '<strong>' . CredentialType::getDescription(CredentialType::ADMIN) . '</strong>'
                        ]) !!}
                    </h5>
                  </div>
                </div>
              </div>
        </div>
        @endunless
    </div>
    @unless (($type->value ?? '') === CredentialType::ADMIN)
    <div class="form-row">
        @include('partials.datatable')
    </div>
    @endunless
    <script>
        credentialTypes = {
            admin: "{{ CredentialType::ADMIN }}",
            analytics: "{{ CredentialType::ANALYTICS }}",
        };
        credentialPermissions = {
            read: "{{ CredentialPermission::READ }}",
            write: "{{ CredentialPermission::WRITE }}",
        };
    </script>
    <div class="form-row mt-4">
        <div class="form-group mb-0 col text-center">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
        </div>
    </div>
    @endcomponent
</form>

