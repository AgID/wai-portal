@extends('layouts.default')

@section('title', __('ui.pages.admin-login.title'))

@section('content')
  <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('admin-do_login') }}">
    @csrf
    <fieldset class="Form-fieldset">
      <div class="Form-field">
        <label class="Form-label is-required" for="email">Indirizzo email{{-- //TODO: put message in lang file --}}</label>
        <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required/>
      </div>
      <div class="Form-field">
        <label class="Form-label is-required" for="password">Password{{-- //TODO: put message in lang file --}}</label>
        <input class="Form-input" id="password" name="password" type="password" aria-required="true" required/>
      </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
      <button type="submit" class="Button Button--default u-text-xs submit">Accedi{{-- //TODO: put message in lang file --}}</button>
      <br>
      <div class="Prose"><a href="{{ route('admin-password_forgot', [], false) }}"><small>Password dimenticata?</small></a></div>
    </div>
  </form>
@endsection
