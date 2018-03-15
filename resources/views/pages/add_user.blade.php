@extends('layouts.default')

@section('title', __('ui.pages.add-user.title'))

@section('content')
  <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('users-store') }}">
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
      <legend class="Form-legend">Informazioni sul nuovo utente{{-- //TODO: put message in lang file --}}</legend>
      <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
        @if ($errors->has('email'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
        <p id="error-email" class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
        @endif
        <label class="Form-label is-required" for="email">Indirizzo email istituzionale{{-- //TODO: put message in lang file --}}</label>
        <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required>
        <p class="Form-message">Es. nome.cognome@agid.gov.it{{-- //TODO: put message in lang file --}}</p>
        @if ($errors->has('email'))</div>@endif
      </div>
      <div class="Form-field {{ $errors->has('fiscalNumber') ? 'is-invalid' : '' }}">
        @if ($errors->has('fiscalNumber'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
        <p id="error-name" class="u-text-p u-padding-r-bottom">{{ $errors->first('fiscalNumber') }}</p>
        @endif
        <label class="Form-label is-required" for="fiscalNumber">Codice fiscale{{-- //TODO: put message in lang file --}}</label>
        <input class="Form-input" id="fiscalNumber" name="fiscalNumber" aria-required="true" value="{{ old('fiscalNumber') }}" required>
        @if ($errors->has('fiscalNumber'))</div>@endif
      </div>
      <div class="Form-field {{ $errors->has('role') ? 'is-invalid' : '' }}">
        @if ($errors->has('role'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
        <p id="error-name" class="u-text-p u-padding-r-bottom">{{ $errors->first('role') }}</p>
        @endif
        <label class="Form-label is-required" for="role">Ruolo <small>(vedi descrizione)</small>{{-- //TODO: put message in lang file --}}</label>
        <select class="Form-input" id="role" name="role" aria-required="true" required>
          <option value="" selected disabled>seleziona</option>
          <option value="reader" {{ old('role') == 'reader' ? "selected" : "" }}>{{ __('auth.roles.reader') }}</option>
          <option value="manager" {{ old('role') == 'manager' ? "selected" : "" }}>{{ __('auth.roles.manager') }}</option>
          <option value="admin" {{ old('role') == 'admin' ? "selected" : "" }}>{{ __('auth.roles.admin') }}</option>
        </select>
        @if ($errors->has('role'))</div>@endif
      </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textRight">
      <button type="submit" class="Button Button--default u-text-xs">Invia{{-- //TODO: put message in lang file --}}</button>
    </div>
  </form>
  <h4 class="u-text-h4 u-border-bottom-xxs">Descrizione dei ruoli</h4>{{-- //TODO: put message in lang file --}}
  <dl class="DescriptionList">
      <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.reader')) }}</dt>
      <dd class="DescriptionList-Description">{{ __('auth.roles.reader_description') }}</dd>
      <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.manager')) }}</dt>
      <dd class="DescriptionList-Description">{{ __('auth.roles.manager_description') }}</dd>
      <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.admin')) }}</dt>
      <dd class="DescriptionList-Description">{{ __('auth.roles.admin_description') }}</dd>
  </dl>
@endsection
