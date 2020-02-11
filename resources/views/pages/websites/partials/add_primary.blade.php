<h3>{{ __('Aggiungi il sito istituzionale') }}</h3>
<p>{{ __('Per iniziare scegli la tua pubblica amministrazione e verifica che i dati siano corretti.') }}</p>
<form method="post" action="{{ route('websites.store.primary') }}" class="needs-validation" novalidate>
    @csrf
    <div class="row mt-5 justify-content-between">
        <div class="col-md-6">
            <div class="form-row">
                <div class="form-group col has-form-text">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-search') }}"></use></svg>
                            </div>
                        </div>
                        <label for="public_administration_name">{{ __('Pubblica amministrazione') }}</label>
                        <input type="search" autocomplete="off" class="form-control autocomplete{{ $errors->has('public_administration_name') ? ' is-invalid' : '' }}" id="public_administration_name" name="public_administration_name" data-search="searchIpa" data-source="{{ route('ipa.search') }}" value="{{ old('public_administration_name') }}" maxlength="255" aria-describedby="pa_name-input-help" aria-required="true" required>
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
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                        </div>
                        <label for="url">{{ __('URL sito istituzionale') }}</label>
                        <input type="text" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}" id="url" name="url" value="{{ old('url') }}" aria-required="true" required readonly>
                        @error('url')
                        <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                        @else
                        <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.url')]) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                        </div>
                        <label for="rtd_name">{{ __('Responsabile ufficio per la transizione al digitale') }}</label>
                        <input type="text" class="form-control{{ $errors->has('rtd_name') ? ' is-invalid' : '' }}" id="rtd_name" name="rtd_name" value="{{ old('rtd_name') }}" readonly>
                        @error('rtd_name')
                        <div class="invalid-feedback">{{ $errors->first('rtd_name') }}</div>
                        @else
                        <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.rtd_name')]) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                        </div>
                        <label for="rtd_mail">{{ __('Indirizzo email responsabile ufficio per la transizione al digitale') }}</label>
                        <input type="text" class="form-control{{ $errors->has('rtd_mail') ? ' is-invalid' : '' }}" id="rtd_mail" name="rtd_mail" value="{{ old('rtd_mail') }}" readonly>
                        @error('rtd_mail')
                        <div class="invalid-feedback">{{ $errors->first('rtd_mail') }}</div>
                        @else
                        <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.rtd_mail')]) }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 text-serif d-flex align-items-center">
            <div id="rtd_mail_present" class="d-none">
                <p>
                    {!! __('Bene, la tua pubblica amministrazione ha indicato su :ipa il recapito del :rtd. 👍🏼', [
                        'ipa' => '<a href="https://www.indicepa.gov.it/" class="external-link" rel="noopener noreferrer">' . __('IndicePA') . '</a>',
                        'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                    ]) !!}
                </p>
                <p>
                    {!! __("Dopo questo passaggio invieremo un messaggio all'indirizzo email del/la :rtd della tua PA per informarlo/a dell'avvenuta richiesta di :onboarding su :app.", [
                        'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                        'onboarding' => '<i>onboarding</i>',
                        'app' => config('app.name'),
                    ]) !!}
                </p>
            </div>
            <div id="rtd_mail_missing" class="d-none">
                <p>
                    {!! __('Sembra che la tua pubblica amministrazione non abbia indicato su :ipa il recapito del/la :rtd. 🙁', [
                        'ipa' => '<a href="https://www.indicepa.gov.it/" class="external-link" rel="noopener noreferrer">' . __('IndicePA') . '</a>',
                        'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                    ]) !!}
                </p>
                <p>
                    {!! __("Questo non ti impedisce di andare avanti con la richiesta di :onboarding su :app per la tua PA. Quando il recapito del/la :rtd sarà disponibile invieremo un messaggio per informarlo/a.", [
                        'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                        'onboarding' => '<i>onboarding</i>',
                        'app' => config('app.name'),
                    ]) !!}
                </p>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6 border-bottom border-md-bottom-0 border-md-right pb-5 pb-md-0 pr-md-5 d-flex flex-column justify-content-between">
            <div class="form-check mb-4">
                <input class="form-control form-check-input" class="{{ $errors->has('correct') ? ' is-invalid' : '' }}" type="checkbox" id="correct_confirmation" name="correct_confirmation" aria-required="true" required>
                <label class="form-check-label" for="correct_confirmation">{{ __("Confermo che i dati sono corretti") }}</label>
                <div class="invalid-feedback">{{ __('validation.accepted', ['attribute' => __('validation.attributes.correct_confirmation')]) }}</div>
            </div>
            <div><button type="submit" class="btn btn-primary">{{ __('Aggiungi il sito') }}</button></div>
        </div>
        <div class="form-group col-md-6 pl-md-5 d-flex flex-column justify-content-between">
            <p>{{ __('Se riscontri delle inesattezze nei dati visualizzati potresti non riuscire a completare la procedura. Interrompi adesso e riprendi dopo che saranno stati corretti.') }}</p>
            <div>
                <a href="https://www.indicepa.gov.it/" role="button" class="btn btn-icon btn-outline-primary" rel="noopener noreferrer">
                    <span class="mr-1">{{ __('I dati NON sono corretti') }}</span>
                    <svg class="icon icon-primary">
                        <use xlink:href="{{ asset('svg/sprite.svg#it-external-link') }}"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <input type="hidden" id="ipa_code" name="ipa_code" value="{{ old('ipa_code') }}"/>
</form>
