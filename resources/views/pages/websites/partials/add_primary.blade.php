
@if (!app()->environment('production') && config('wai.custom_public_administrations', false) && !Auth::user()->isA(UserRole::SUPER_ADMIN))
<ul class="nav nav-tabs auto pt-3">
    <li class="nav-item"><a class="nav-link @if(Route::is('websites.index') || Route::is('publicAdministrations.add')) active @endif" href="{{ route('publicAdministrations.add') }}">Usa IndicePA</a></li>
    <li class="nav-item"><a class="nav-link @if(Route::is('websites.create.primary.custom')) active @endif" href="{{ route('websites.create.primary.custom') }}">Crea la tua pubblica amministrazione</a></li>
</ul>
@endif

<div class="container @if (!app()->environment('production') && config('wai.custom_public_administrations', false)) pt-5 @endif ">

    @unless ($hideTitle ?? false)
    <h3>{{ __('Aggiungi il sito istituzionale') }}</h3>
    @endunless

    @if($customForm)
        <p>{{ __('Per iniziare senza usare una pubblica amministrazione esistente crea in questa pagina la tua pubblica amministrazione personalizzata.') }}</p>
    @else
        <p>{{ __("Per iniziare scegli la tua pubblica amministrazione usando IndicePA.") }}</p>
    @endif

    <form method="post" action="{{ route('websites.store.primary') }}" class="needs-validation @if($customForm) website-custom-form @endif" novalidate>
        @csrf
        <div class="row mt-5 justify-content-between">
            <div class="col-md-6">
                <div class="form-row">
                    <div class="form-group col @if($customForm) has-custom-form-text @else has-form-text @endif">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <svg class="icon icon-sm"><use xlink:href="{{ $customForm ? asset('svg/sprite.svg#it-pa') : asset('svg/sprite.svg#it-search') }}"></use></svg>
                                </div>
                            </div>
                            <label for="public_administration_name">{{ $customForm ? __('Nome della pubblica amministrazione') : __('Pubblica amministrazione') }}</label>

                            <input type="search" autocomplete="off"
                                class="form-control autocomplete{{ $errors->has('public_administration_name') ? ' is-invalid' : '' }}"
                                id="public_administration_name" name="public_administration_name"
                                @unless ($customForm) data-search="searchIpa" data-source="{{ route('ipa.search') }}" @endunless
                                value="{{ old('public_administration_name') }}"
                                maxlength="255" aria-describedby="pa_name-input-help" aria-required="true" required >

                            @unless ($customForm)
                                <ul class="autocomplete-list"></ul>
                                <div class="searching-icon input-group-append">
                                    <div class="input-group-text">
                                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-settings') }}"></use></svg>
                                    </div>
                                </div>
                            @endunless

                            @error('public_administration_name')
                            <div class="invalid-feedback">{{ $errors->first('public_administration_name') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.public_administration_name')]) }}</div>
                            @enderror
                        </div>

                        @unless ($customForm)
                        <small id="pa_name-input-help" class="form-text text-muted">{{ __('Per la ricerca puoi usare il nome, il codice IPA e il luogo dove si trova la tua PA.') }}</small>
                        @endunless
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col @if($customForm) has-form-text @endif">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                            </div>
                            <label for="url">{{ __('URL sito istituzionale') }}</label>

                            <input type="text" class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
                                id="url" name="url" aria-describedby="url-input-help" value="{{ old('url') }}" aria-required="true" required @unless ($customForm) readonly @endunless >

                            @error('url')
                            <div class="invalid-feedback">{{ $errors->first('url') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.required', ['attribute' => __('validation.attributes.url')]) }}</div>
                            @enderror
                        </div>
                        @if($customForm)
                        <small id="url-input-help" class="form-text text-muted">{{ __('Inserisci una URL completa di http:// o https://') }}</small>
                        @endif
                    </div>
                </div>
                @unless ($customForm)
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg></div>
                            </div>
                            <label for="rtd_name">{{ __('Responsabile ufficio per la transizione al digitale') }}</label>
                            <input type="text" class="form-control{{ $errors->has('rtd_name') ? ' is-invalid' : '' }}"
                                id="rtd_name" name="rtd_name" value="{{ old('rtd_name') }}" @unless ($customForm) readonly @endunless >
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
                            <input type="text" class="form-control{{ $errors->has('rtd_mail') ? ' is-invalid' : '' }}"
                                id="rtd_mail" name="rtd_mail" value="{{ old('rtd_mail') }}" @unless ($customForm) readonly @endunless >
                            @error('rtd_mail')
                            <div class="invalid-feedback">{{ $errors->first('rtd_mail') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.attributes.rtd_mail', ['attribute' => __('validation.attributes.rtd_mail')]) }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endunless
                <div class="form-row">
                    <div class="form-group col has-form-text ">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use></svg></div>
                            </div>
                            <label for="email">{{ __('Il tuo indirizzo email di lavoro') }}</label>
                            <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" id="email" name="email" value="{{ old('email') ? old('email') : auth()->user()->email }}" maxlength="75" aria-describedby="email-input-help" aria-required="true" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.email', ['attribute' => __('validation.attributes.email')]) }}</div>
                            @enderror
                        </div>
                        <small id="email-input-help" class="form-text text-muted">{{ __("L'inidirizzo email al quale vuoi ricevere le comunicazioni per questa pubblica amministrazione.") }}</small>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-serif d-flex flex-column justify-content-start">
                @env('public-playground')
                <div class="callout callout-highlight danger">
                    <div class="callout-title">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-warning-circle') }}"></use></svg>
                        {{ __('avviso') }}
                    </div>
                    <p>
                        {{ __("Ti ricordiamo che tutti i dati memorizzati sono automaticamente resettati ogni fine settimana.") }}
                    </p>
                </div>
                @endenv
                @unless ($customForm)
                <div id="primary_website_missing" class="callout callout-highlight danger d-none">
                    <div class="callout-title">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-close-circle') }}"></use></svg>
                        {{ __('errore') }}
                    </div>
                    <p>
                        {!! __("Sembra che la tua pubblica amministrazione non abbia indicato su :ipa l'indirizzo web del :primary_website. 🙁", [
                            'ipa' => '<a href="https://www.indicepa.gov.it/" class="external-link" target="_blank" rel="noopener noreferrer">' . __('IndicePA') . '</a>',
                            'primary_website' => '<strong>' . __('sito web istituzionale') . '</strong>',
                        ]) !!}
                    </p>
                    <p>
                        {!! __("Purtroppo questo :prevents di andare avanti con la richiesta di :onboarding su :app per la tua PA. Quando l'indirizzo web del sito istituzionale sarà stato inserito, torna per completare la procedura.", [
                            'prevents' => '<strong>ti impedisce</strong>',
                            'onboarding' => '<i>onboarding</i>',
                            'app' => config('app.name'),
                        ]) !!}
                    </p>
                </div>
                <div id="rtd_mail_present" class="callout callout-highlight success d-none">
                    <div class="callout-title">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-check-circle') }}"></use></svg>
                        {{ __('tutto ok') }}
                    </div>
                    <p>
                        {!! __('Bene, la tua pubblica amministrazione ha indicato su :ipa il recapito del :rtd. 👍🏼', [
                            'ipa' => '<a href="https://www.indicepa.gov.it/" class="external-link" target="_blank" rel="noopener noreferrer">' . __('IndicePA') . '</a>',
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                        ]) !!}
                    </p>
                    @unlessenv ('public-playground')
                    <p>
                        {!! __("Dopo questo passaggio invieremo un messaggio all'indirizzo email del/la :rtd della tua PA per informarlo/a dell'avvenuta richiesta di :onboarding su :app.", [
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                            'onboarding' => '<i>onboarding</i>',
                            'app' => config('app.name'),
                        ]) !!}
                    </p>
                    @else
                    <p>
                        {!! __(':nb: in questo ambiente (public playground) NON sarà inviata alcuna mail al/la :rtd.', [
                            'nb' => '<strong>' . __('Nota bene') . '</strong>',
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>'
                        ]) !!}
                    </p>
                    @endenv
                </div>
                <div id="rtd_mail_missing" class="callout callout-highlight warning d-none">
                    <div class="callout-title">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-info-circle') }}"></use></svg>
                        {{ __('attenzione') }}
                    </div>
                    <p>
                        {!! __('Sembra che la tua pubblica amministrazione non abbia indicato su :ipa il recapito del/la :rtd. 🙁', [
                            'ipa' => '<a href="https://www.indicepa.gov.it/" class="external-link" target="_blank" rel="noopener noreferrer">' . __('IndicePA') . '</a>',
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>',
                        ]) !!}
                    </p>
                    <p>
                        {!! __("Questo non ti impedisce di andare avanti con la richiesta di :onboarding su :app per la tua PA.", [
                            'onboarding' => '<i>onboarding</i>',
                            'app' => config('app.name'),
                        ]) !!}
                        @unlessenv ('public-playground')
                        <br><br>
                        {!! __('Quando il recapito del/la :rtd sarà disponibile invieremo un messaggio per informarlo/a.', [
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>'
                        ]) !!}
                        @else
                        <br><br>
                        {!! __(':nb: in questo ambiente (public playground) NON sarà inviata alcuna mail al/la :rtd.', [
                            'nb' => '<strong>' . __('Nota bene') . '</strong>',
                            'rtd' => '<strong>' . __('Responsabile ufficio per la transizione al digitale') . '</strong>'
                        ]) !!}
                        @endenv
                    </p>
                </div>
                @endunless
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 border-bottom border-md-bottom-0 @unless ($customForm) border-md-right @endunless pb-5 pb-md-0 pr-md-5 d-flex flex-column justify-content-between">
                @if($customForm)
                <input type="hidden" id="correct_confirmation" name="correct_confirmation" value="true"/>
                @else
                <div class="form-check mb-4">
                    <input class="form-control form-check-input{{ $errors->has('correct_confirmation') ? ' is-invalid' : '' }}" type="checkbox" id="correct_confirmation" name="correct_confirmation" aria-required="true" required>
                    <label class="form-check-label" for="correct_confirmation">{{ __("Confermo che i dati sono corretti") }}</label>
                    <div class="invalid-feedback">{{ __('validation.accepted', ['attribute' => __('validation.attributes.correct_confirmation')]) }}</div>
                </div>
                @endif
                <div><button type="submit" class="btn btn-primary">{{ __('Aggiungi il sito') }}</button></div>
            </div>

            @unless ($customForm)
            <div class="form-group col-md-6 pl-md-5 d-flex flex-column justify-content-between">
                <p>{{ __('Se riscontri delle inesattezze nei dati visualizzati potresti non riuscire a completare la procedura. Interrompi adesso e riprendi dopo che saranno stati corretti.') }}</p>
                <div>
                    <a href="https://www.indicepa.gov.it/" role="button" class="btn btn-icon btn-outline-primary" target="_blank" rel="noopener noreferrer">
                        <span class="mr-1">{{ __('I dati NON sono corretti') }}</span>
                        <svg class="icon icon-primary">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-external-link') }}"></use>
                        </svg>
                    </a>
                </div>
            </div>
            @endunless
        </div>
        @unless ($customForm)
        <input type="hidden" id="ipa_code" name="ipa_code" value="{{ old('ipa_code') }}"/>
        @else
        <input type="hidden" id="website_type" name="website_type" value="custom" />
        @endunless
    </form>
</div>
