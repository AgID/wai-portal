@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Siti web'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    <h3>{{ __('Crea una pubblica amministrazione') }}</h3>
    <form method="post" action="{{ route('websites.store.primary') }}" class="needs-validation" novalidate>
        @csrf
        <div class="row mt-5 justify-content-between">
            <div class="col-md-6">
                <div class="form-row">
                    <div class="form-group col has-form-text">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use></svg>
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
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use></svg></div>
                            </div>
                            <label for="site">{{ __('URL sito istituzionale') }}</label>
                            <input type="text" class="form-control{{ $errors->has('site') ? ' is-invalid' : '' }}" id="site" name="site" value="{{ old('site') }}" aria-required="true" required>
                            @error('site')
                            <div class="invalid-feedback">{{ $errors->first('site') }}</div>
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
                            <input type="text" class="form-control{{ $errors->has('rtd_name') ? ' is-invalid' : '' }}" id="rtd_name" name="rtd_name" value="{{ old('rtd_name') }}">
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
                            <input type="text" class="form-control{{ $errors->has('rtd_mail') ? ' is-invalid' : '' }}" id="rtd_mail" name="rtd_mail" value="{{ old('rtd_mail') }}">
                            @error('rtd_mail')
                            <div class="invalid-feedback">{{ $errors->first('rtd_mail') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.rtd_mail')]) }}</div>
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
                            <label for="rtd_pec">{{ __('Indirizzo pec responsabile ufficio per la transizione al digitale') }}</label>
                            <input type="text" class="form-control{{ $errors->has('rtd_pec') ? ' is-invalid' : '' }}" id="rtd_pec" name="rtd_pec" value="{{ old('rtd_pec') }}">
                            @error('rtd_pec')
                            <div class="invalid-feedback">{{ $errors->first('rtd_pec') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.rtd_pec')]) }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-map-marker') }}"></use></svg></div>
                            </div>
                            <label for="city">{{ __('Città') }}</label>
                            <input type="text" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" id="city" name="city" value="{{ old('city') }}" required>
                            @error('city')
                            <div class="invalid-feedback">{{ $errors->first('city') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.city')]) }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-map-marker') }}"></use></svg></div>
                            </div>
                            <label for="county">{{ __('Provincia') }}</label>
                            <input type="text" class="form-control{{ $errors->has('county') ? ' is-invalid' : '' }}" id="county" name="county" value="{{ old('county') }}" required>
                            @error('county')
                            <div class="invalid-feedback">{{ $errors->first('county') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.county')]) }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-map-marker') }}"></use></svg></div>
                            </div>
                            <label for="region">{{ __('Regione') }}</label>
                            <input type="text" class="form-control{{ $errors->has('region') ? ' is-invalid' : '' }}" id="region" name="region" value="{{ old('region') }}" required>
                            @error('region')
                            <div class="invalid-feedback">{{ $errors->first('region') }}</div>
                            @else
                            <div class="invalid-feedback">{{ __('validation.mail', ['attribute' => __('validation.attributes.region')]) }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 border-bottom border-md-bottom-0 pb-5 pb-md-0 pr-md-5 d-flex flex-column justify-content-between">
                <div><button type="submit" class="btn btn-primary">{{ __('Crea il sito') }}</button></div>
            </div>
        </div>
    </form>
    @endcomponent
@endsection
