@extends('layouts.default')

@section('title', __('ui.pages.admin-password_forgot.title'))

@section('content')
  <div class="Prose u-layout-prose"><p>Se non riesci ad accedere al tuo account amministrativo, puoi richiedere un reset della password inserendo il tuo indirizzo email nel modulo presente in questa pagina.</p></div>
  <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('admin-send_reset_password') }}">
    @csrf
    <fieldset class="Form-fieldset">
      <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
        @if ($errors->has('email'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
        <p id="error-email" class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
        @endif
        <label class="Form-label is-required" for="email">Indirizzo email{{-- //TODO: put message in lang file --}}</label>
        <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required/>
        @if ($errors->has('email'))</div>@endif
      </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
      <button type="submit" class="Button Button--default u-text-xs submit">Richiedi reset password{{-- //TODO: put message in lang file --}}</button>
    </div>
  </form>
@endsection
