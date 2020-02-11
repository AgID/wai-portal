<form method="post" action="{{ $route }}" class="needs-validation" novalidate>
    @csrf
    @isset($website)
    @method('put')
    @endisset
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3 class="section-header">{{ __('Informazioni') }}</h3>
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-help-circle') }}"></use></svg></div>
                </div>
                <label for="website_name">{{ ucfirst(__('nome del sito')) }}</label>
                <input type="text" class="form-control{{ $errors->has('website_name') ? ' is-invalid' : '' }}" id="website_name" name="website_name" value="{{ old('website_name', $website->name ?? '') }}" maxlength="255" aria-describedby="name-input-help" aria-required="true" required {{ optional($website->type ?? null)->is(WebsiteType::PRIMARY) ? 'readonly' : '' }}>
                @error('website_name')
                <div class="invalid-feedback">{{ $errors->first('website_name') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.website_name')]) }}</div>
                @enderror
            </div>
            <small id="name-input-help" class="form-text text-muted">{{ __('Inserisci il nome del sito: una buona scelta potrebbe essere il titolo della pagina iniziale.') }}</small>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                </div>
                <label for="url">{{ __('URL') }}</label>
                <input type="text" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" id="url" name="url" value="{{ old('url', $website->url ?? '') }}" maxlength="255" aria-describedby="url-input-help" aria-required="true" required {{ optional($website->type ?? null)->is(WebsiteType::PRIMARY) ? 'readonly' : '' }}>
                @error('url')
                <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.url', ['attribute' => __('validation.attributes.url')]) }}</div>
                @enderror
            </div>
            <small id="url-input-help" class="form-text text-muted">{!! __("Inserisci l'indirizzo del sito completo del protocollo :http o :https (es. https://www.agid.gov.it).", ['http' => '<code>http://</code>', 'https' => '<code>https://</code>']) !!}</small>
        </div>
    </div>
    @unless (optional($website->type ?? null)->is(WebsiteType::PRIMARY))
    <div class="form-row">
        <div class="form-group has-form-text col-md-7">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-folder') }}"></use></svg></div>
                </div>
                <div class="bootstrap-select-wrapper form-control flex-grow-1{{ $errors->has('type') ? ' is-invalid' : '' }}">
                    <label for="type">{{ ucfirst(__('tipologia')) }}</label>
                    <select title="Scegli una tipologia" id="type" name="type" aria-required="true" required>
                    @foreach(WebsiteType::toSelectArray() as $value => $label)
                        @unless ($value === WebsiteType::PRIMARY)
                        <option value="{{ $value }}" {{ old('type', $website->type->value ?? '') == $value ? "selected" : "" }}>{{ $label }}</option>
                        @endif
                    @endforeach
                    </select>
                </div>
                @error('type')
                <div class="invalid-feedback">{{ $errors->first('type') }}</div>
                @else
                <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.type')]) }}</div>
                @enderror
            </div>
            <small id="type-input-help" class="form-text text-muted">
                {{ __('Non sai quale tipologia scegliere per il sito della tua PA?') }}
                <a href="{{ route('faq') }}#tipologie-siti">{{ __('Consulta le FAQ') }}</a>
            </small>
        </div>
    </div>
    @endunless
    <h3 class="section-header{{ $errors->has('permissions') ? ' is-invalid' : '' }}">{{ __('permessi degli utenti') }}</h3>
    @error('permissions')
    <div class="invalid-feedback">{{ $errors->first('permissions') }}</div>
    @enderror
    <h6>{{ __('Puoi assegnare permessi diversificati per ciascun utente') }}</h6>
    <div class="row justify-content-between">
        <div class="col-md-6">
            <p><small>
                {{ UserPermission::getLongDescription(UserPermission::READ_ANALYTICS) }}<br>
                {{ UserPermission::getLongDescription(UserPermission::MANAGE_ANALYTICS) }}
            </small></p>
        </div>
        <div class="col-md-5">
            <div class="callout callout-highlight note m-0">
                <p class="font-italic"><small>{{ __('I permessi degli utenti con ruolo di amministratore non possono essere modificati.') }}</small></p>
            </div>
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
