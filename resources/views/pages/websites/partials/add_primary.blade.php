<h3>{{ __('Aggiungi il sito istituzionale') }}</h3>
<p>{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin cursus felis quam, quis varius quam ornare ullamcorper. Cras eget lacinia justo, non finibus odio. Nullam in mi ut enim pulvinar posuere. Morbi vel ante in ligula tincidunt ultrices.') }}</p>
<form method="post" action="{{ route('websites.store.primary') }}" class="needs-validation" novalidate>
    @csrf
    <div class="mt-5">
        <div class="form-row">
            <div class="form-group has-form-text col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-search') }}"></use></svg>
                        </div>
                    </div>
                    <label for="public_administration_name">{{ __('Pubblica amministrazione') }}</label>
                    <input type="search" class="form-control autocomplete{{ $errors->has('public_administration_name') ? ' is-invalid' : '' }}" id="public_administration_name" name="public_administration_name" data-search="searchIpa" data-source={{ route('ipa.search') }} value="{{ old('public_administration_name') }}" placeholder="{{ __('cerca la tua pubblica amministrazione') }}" aria-labelledby="pa_name-input-help" aria-required="true" required>
                    <ul class="autocomplete-list"></ul>
                    <div class="searching-icon input-group-append">
                        <div class="input-group-text">
                            <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-settings') }}"></use></svg>
                        </div>
                    </div>
                    @error('public_administration_name')
                    <div class="invalid-feedback">{{ $errors->first('public_administration_name') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.public_administration_name')]) }}</div>
                    @enderror
                </div>
                <small id="pa_name-input-help" class="form-text text-muted">{{ __('Per la ricerca puoi usare il nome, il codice IPA e il luogo dove si trova la tua PA.') }}</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                    </div>
                    <label for="url">{{ __('URL sito istituzionale') }}</label>
                    <input type="text" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" id="url" name="url" value="{{ old('url') }}" placeholder="{{ __('sito istituzionale') }}" aria-required="true" required readonly>
                    @error('url')
                    <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.url')]) }}</div>
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
                    <label for="rtd_name">{{ __('Responsabile ufficio per la transizione al digitale') }}</label>
                    <input type="text" class="form-control{{ $errors->has('rtd_name') ? ' is-invalid' : '' }}" id="rtd_name" name="rtd_name" value="{{ old('rtd_name') }}" placeholder="{{ __('nominativo responsabile') }}" readonly>
                    @error('rtd_name')
                    <div class="invalid-feedback">{{ $errors->first('rtd_name') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.rtd_name')]) }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                    </div>
                    <label for="rtd_mail">{{ __('Indirizzo email responsabile ufficio per la transizione al digitale') }}</label>
                    <input type="text" class="form-control{{ $errors->has('rtd_mail') ? ' is-invalid' : '' }}" id="rtd_mail" name="rtd_mail" value="{{ old('rtd_mail') }}" placeholder="{{ __('recapito email responsabile') }}" readonly>
                    @error('rtd_mail')
                    <div class="invalid-feedback">{{ $errors->first('rtd_mail') }}</div>
                    @else
                    <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.rtd_mail')]) }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 border-md-right pr-md-5 d-flex flex-column justify-content-between">
                <div class="form-check mb-4">
                    <input class="form-control form-check-input" class="{{ $errors->has('correct') ? ' is-invalid' : '' }}" type="checkbox" id="correct_confirmation" name="correct_confirmation" aria-required="true" required>
                    <label class="form-check-label" for="correct_confirmation">{{ __("Confermo che i dati sono corretti") }}</label>
                    <div class="invalid-feedback">{{ __('validation.accepted', ['attribute' => __('validation.attributes.correct_confirmation')]) }}</div>
                </div>
                <div><button type="submit" class="btn btn-primary">{{ __('Inserisci sito') }}</button></div>
            </div>
            <div class="form-group col-md-6 pl-md-5 d-flex flex-column justify-content-between">
                <p>{{ __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin cursus felis quam, quis varius quam ornare ullamcorper.') }}</p>
                <div>
                    <a href="#" role="button" class="btn btn-icon btn-outline-primary">
                        <span class="mr-1">{{ __('I dati NON sono corretti') }}</span>
                        <svg class="icon icon-primary">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-external-link') }}"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="ipa_code" name="ipa_code" value="{{ old('ipa_code') }}"/>
</form>
