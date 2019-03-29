@extends('layouts.default')

@section('title', __('ui.pages.websites.edit.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('websites-update', ['website' => $website], false) }}">
        @csrf
        @if ($errors->isEmpty())
            <div class="Prose Alert Alert--info">
                <p class="u-text-p">Tutti i campi sono richiesti salvo dove espressamente indicato.</p>
            </div>
        @else
            <div class="Alert Alert--error Alert--withIcon u-margin-r-bottom" role="alert">
                <p class="u-text-p">
                    È necessario correggere alcuni errori prima di poter inviare il modulo.
                    {{-- //TODO: put message in lang file --}}
                </p>
            </div>
        @endif
        <fieldset class="Form-fieldset">
            <legend class="Form-legend">
                Informazioni del sito{{-- //TODO: put message in lang file --}}
            </legend>
            <div class="Form-field">
                <label class="Form-label is-required" for="name">
                    Nome della pubblica amministrazione di appartenenza{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input is-disabled" id="name" name="name" value="{{ $website->publicAdministration->name }}" aria-required="true" required readonly>
            </div>
            <div class="Form-field {{ $errors->has('name') ? 'is-invalid' : '' }}">
                @if ($errors->has('name'))
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('name') }}</p>
                        @endif
                        <label class="Form-label is-required" for="name">
                            Nome del sito web{{-- //TODO: put message in lang file --}}
                        </label>
                        <input class="Form-input" id="name" name="name" aria-required="true" value="{{ old('name') ?? $website->name }}" required/>
                        @if ($errors->has('name'))
                    </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('url') ? 'is-invalid' : '' }}">
                @if ($errors->has('url'))
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('url') }}</p>
                        @endif
                        <label class="Form-label is-required" for="url">
                            Indirizzo del sito web{{-- //TODO: put message in lang file --}}
                        </label>
                        <input class="Form-input" id="url" name="url" aria-required="true" value="{{ old('url') ?? $website->url }}" required/>
                        <p class="Form-message">
                            Inserisci l'indirizzo del sito completo del protocollo <code>http://</code> o <code>https://</code> (es. https://www.agid.gov.it).{{-- //TODO: put message in lang file --}}
                        </p>
                        @if ($errors->has('url'))
                    </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('type') ? 'is-invalid' : '' }}">
                @if ($errors->has('type'))
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('type') }}</p>
                        @endif
                        <label class="Form-label is-required" for="type">
                            Tipologia{{-- //TODO: put message in lang file --}}
                        </label>
                        <select class="Form-input" id="type" name="type" aria-required="true" required/>
                        <option value="">seleziona</option>{{-- //TODO: use localized enum --}}
                        <option value="secondary" {{ (old('type') ?? $website->type) == 'secondary' ? "selected" : "" }}>{{ __('ui.website.secondary') }}</option>
                        <option value="webapp" {{ (old('type') ?? $website->type) == 'webapp' ? "selected" : "" }}>{{ __('ui.website.webapp') }}</option>
                        <option value="testing" {{ (old('type') ?? $website->type) == 'testing' ? "selected" : "" }}>{{ __('ui.website.testing') }}</option>
                        </select>
                        @if ($errors->has('type'))
                    </div>
                @endif
            </div>
        </fieldset>
        <div class="Form-field Grid-cell u-textRight">
            <button type="submit" class="Button Button--default u-text-xs">
                Invia{{-- //TODO: put message in lang file --}}
            </button>
        </div>
    </form>
@endsection
