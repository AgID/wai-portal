@extends('layouts.default')

@section('title', __('ui.pages.add-website.title'))

@section('content')
<form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('websites-store') }}">
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
    <legend class="Form-legend">Informazioni del nuovo sito{{-- //TODO: put message in lang file --}}</legend>
    <div class="Form-field">
      <label class="Form-label is-required" for="name">Nome della pubblica amministrazione di appartenenza{{-- //TODO: put message in lang file --}}</label>
      <input class="Form-input is-disabled" id="name" name="name" value="{{ auth()->user()->publicAdministration->name }}" aria-required="true" required readonly>
    </div>
    <div class="Form-field {{ $errors->has('site') ? 'is-invalid' : '' }}">
      @if ($errors->has('site'))
      <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
      <p id="error-name" class="u-text-p u-padding-r-bottom">{{ $errors->first('site') }}</p>
      @endif
      <label class="Form-label is-required" for="site">Dominio del sito web{{-- //TODO: put message in lang file --}}</label>
      <input class="Form-input" id="site" name="site" aria-required="true" value="{{ old('site') }}" required>
      @if ($errors->has('site'))</div>@endif
    </div>
    <div class="Form-field {{ $errors->has('type') ? 'is-invalid' : '' }}">
      @if ($errors->has('type'))
      <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
      <p id="error-name" class="u-text-p u-padding-r-bottom">{{ $errors->first('type') }}</p>
      @endif
      <label class="Form-label is-required" for="type">Tipologia{{-- //TODO: put message in lang file --}}</label>
      <select class="Form-input" id="type" name="type" aria-required="true" required>
        <option value="">seleziona</option>
        <option value="secondary" {{ old('type') == 'secondary' ? "selected" : "" }}>{{ __('ui.website.secondary') }}</option>
        <option value="webapp" {{ old('type') == 'webapp' ? "selected" : "" }}>{{ __('ui.website.webapp') }}</option>
        <option value="testing" {{ old('type') == 'testing' ? "selected" : "" }}>{{ __('ui.website.testing') }}</option>
      </select>
      @if ($errors->has('type'))</div>@endif
    </div>
  </fieldset>
  <input type="hidden" name="ipa_code" value="{{ auth()->user()->publicAdministration->ipa_code }}"/>
  <div class="Form-field Grid-cell u-textRight">
    <button type="submit" class="Button Button--default u-text-xs">Invia{{-- //TODO: put message in lang file --}}</button>
  </div>
</form>

@endsection
