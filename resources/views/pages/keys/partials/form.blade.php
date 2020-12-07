<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($key)
    @method('put')
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3 class="section-header">{{__('Informazioni Chiave')}}</h3>
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-help-circle') }}"></use></svg></div>
                </div>
                <label for="key_name">{{ ucfirst(__('nome della chiave')) }}</label>
                <input 
                    type="text" 
                    class="form-control{{ $errors->has('key_name') ? ' is-invalid' : '' }}" 
                    id="key_name" 
                    name="key_name" 
                    value="{{ old('key_name', $key->client_name ?? '') }}" 
                    maxlength="255" 
                    aria-describedby="name-input-help" 
                    aria-required="true" 
                    required 
                    >
                @error('key_name')
                <div class="invalid-feedback">{{ $errors->first('key_name') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.key_name')]) }}</div>
                @enderror
            </div>
            <small id="name-input-help" class="form-text text-muted">{{ __('Inserisci il nome della chiave.') }}</small>
        </div>
    </div>
    <div class="form-row">
        <h4 class="text-uppercase m-0">{{ __('PERMESSI SUI SITI WEB') }}</h4>
            @include('partials.datatable')
    </div>
    <div class="form-row mt-4">
        <div class="form-group mb-0 col text-center">
            <button type="submit" class="btn btn-primary">{{ __('Salva') }}</button>
        </div>
    </div>
    @endcomponent
    
</form>

