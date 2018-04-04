@extends('layouts.page')

@section('title', __('ui.pages.admin-verify.title'))

@section('page-content')
{{-- //TODO: allow to change email address --}}
<form class="Form Form--spaced u-text-r-xs" method="get" action="{{ route('admin-do_verify', [], false) }}">
    <fieldset class="Form-fieldset">
        <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @if ($errors->has('email'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p id="error-email" class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
                    @endif
                    <label class="Form-label is-required" for="email">
                        Indirizzo email{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                </div>
            @endif
        </div>
        <div class="Form-field">
            <label class="Form-label is-required" for="token">
                Codice di verifica{{-- //TODO: put message in lang file --}}
            </label>
            <input class="Form-input" id="token" name="token" aria-required="true" required/>
            <p class="Form-message">
                Il codice di verifica ricevuto al proprio indirizzo email{{-- //TODO: put message in lang file --}}
            </p>
        </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
        <button type="submit" class="Button Button--default u-text-xs submit">
            Conferma{{-- //TODO: put message in lang file --}}
        </button>
        <br>
        <div class="Prose">
            <a href="{{ route('admin-verify_resend', [], false) }}">
                <small>Codice di verifica non ricevuto?</small>
            </a>
        </div>
    </div>
</form>
@endsection
