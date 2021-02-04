<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($credential)
    @method('put')
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3 class="section-header">{{__('Informazioni Credenziale')}}</h3>
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-help-circle') }}"></use></svg></div>
                </div>
                <label for="credential_name">{{ ucfirst(__('nome della Credenziale')) }}</label>
                <input 
                    type="text" 
                    class="form-control{{ $errors->has('credential_name') ? ' is-invalid' : '' }}" 
                    id="credential_name" 
                    name="credential_name" 
                    value="{{ old('credential_name', $credential->client_name ?? '') }}" 
                    maxlength="255" 
                    aria-describedby="name-input-help" 
                    aria-required="true" 
                    required 
                    >
                @error('credential_name')
                <div class="invalid-feedback">{{ $errors->first('credential_name') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.credential_name')]) }}</div>
                @enderror
            </div>
            <small id="name-input-help" class="form-text text-muted">{{ __('Inserisci il nome della Credenziale.') }}</small>
        </div>
    </div>
    <div class="form-row">
        <h4 class="text-uppercase m-0">{{ __('PERMESSI SUI SITI WEB') }}</h4>
        
    </div>
    <div class="form-row mt-5">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-key') }}"></use></svg></div>
                </div>
                <div class="bootstrap-select-wrapper form-control flex-grow-1{{ $errors->has('type') ? ' is-invalid' : '' }}">
                    <label for="type">{{ ucfirst(__('tipologia')) }}</label>
                    <select title="Scegli una tipologia" id="type" name="type" aria-required="true" required>
                        <option 
                        value="admin"
                        @isset($type)
                            {{ $type === "admin" ? "selected" : "" }}
                        @endisset
                        
                        >
                            Amministratore
                        </option>
                        <option 
                        value="analytics"
                        @isset($type) 
                            {{ $type === "analytics" ? "selected" : "" }}
                        @endisset
                        >
                            Analytics
                        </option>
                    </select>
                </div>
                @error('type')
                <div class="invalid-feedback">{{ $errors->first('type') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.type')]) }}</div>
                @enderror
            </div>
            <small id="type-input-help" class="form-text text-muted">
                {{ __('L\'amministratore può gestire tutti i siti web e tutti gli utenti') }}
            </small>     
        </div>
    </div>
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

