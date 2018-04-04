@extends('layouts.page')

@section('title', __('ui.pages.auth-verify.title'))

@section('page-content')
{{-- //TODO: allow to change email address --}}
<form class="Form Form--spaced u-text-r-xs" method="get" action="{{ route('auth-do_verify', [], false) }}">
    <fieldset class="Form-fieldset">
        <div class="Form-field">
            <label class="Form-label is-required" for="token">
                Codice di verifica{{-- //TODO: put message in lang file --}}
            </label>
            <input class="Form-input" id="token" name="token" aria-required="true" required/>
            <p class="Form-message">
                Il codice di verifica ricevuto all'indirizzo {{ auth()->user()->email }}{{-- //TODO: put message in lang file --}}
            </p>
        </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
        <button type="submit" class="Button Button--default u-text-xs submit">
            Conferma{{-- //TODO: put message in lang file --}}
        </button>
        <a role="button" href="{{ route('auth-verify_resend', [], false) }}" class="Button Button--default u-text-xs submit">
            Rispedisci codice di verifica{{-- //TODO: put message in lang file --}}
        </a>
    </div>
</form>
@endsection
