@extends('layouts.default')

@section('title', __('ui.pages.admin-password_change.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('admin-do_password_change', [], false) }}">
        @csrf
        <fieldset class="Form-fieldset">
            <div class="Form-field {{ $errors->has('password') ? 'is-invalid' : '' }}">
                @if ($errors->has('password'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('password') }}</p>
                @endif
                    <label class="Form-label is-required" for="password">
                        Nuova password{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="password" name="password" type="password" aria-required="true" required>
                    <p class="Form-message">
                        Deve essere lunga almeno 8 caratteri e contenere maiuscole, minuscole, numeri e simboli.{{-- //TODO: put message in lang file --}}
                    </p>
                @if ($errors->has('password'))
                </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('password') ? 'is-invalid' : '' }}">
                @if ($errors->has('password'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <pclass="u-text-p u-padding-r-bottom">{{ $errors->first('password_confirmation') }}</p>
                @endif
                    <label class="Form-label is-required" for="password_confirmation">
                        Conferma password{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="password_confirmation" name="password_confirmation" type="password" aria-required="true" required>
                @if ($errors->has('password'))
                </div>
                @endif
            </div>
        </fieldset>
        <div class="Form-field Grid-cell u-textCenter">
            <button type="submit" class="Button Button--default u-text-xs submit">
                Cambia password{{-- //TODO: put message in lang file --}}
            </button>
        </div>
    </form>
@endsection
