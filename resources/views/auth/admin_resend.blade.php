@extends('layouts.page')

@section('title', __('ui.pages.admin-verify_resend.title'))

@section('page-content')
{{-- //TODO: allow to change email address --}}
<form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('admin-do_verify_resend', [], false) }}">
    @csrf
    <fieldset class="Form-fieldset">
        <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @if ($errors->has('email'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
                    @endif
                    <label class="Form-label is-required" for="email">
                        Indirizzo email{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required>
                    @if ($errors->has('email'))
                </div>
            @endif
        </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
        <button type="submit" class="Button Button--default u-text-xs submit">
            Rispedisci codice di verifica{{-- //TODO: put message in lang file --}}
        </button>
    </div>
</form>
@endsection
