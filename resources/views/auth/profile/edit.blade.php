@extends('layouts.default')

@section('title', __('ui.pages.profile.edit.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route($user->isA('super-admin') ? 'admin.profile.update' : 'user.profile.update') }}">
        @method('patch')
        @csrf
        @if ($user->isA('super-admin'))
            <div class="Prose Alert Alert--warning">
                <p class="u-text-p">
                    L'indirizzo email è una crendenziale ed è usato per il recupero della password.
                    Dal prossimo accesso dovrà essere utilizzato il nuovo indirizzo email.
                </p>
            </div>
        @endif
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
                Informazioni dell'utente{{-- //TODO: put message in lang file --}}
            </legend>
            <div class="Form-field">
                <label class="Form-label" for="name">
                    Nome{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input is-disabled" id="name" name="name" type="text" aria-required="true" value="{{ $user->name }}" required readonly>
            </div>
            <div class="Form-field">
                <label class="Form-label" for="familyName">
                    Cognome{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input is-disabled" id="familyName" name="familyName" type="text" aria-required="true" value="{{ $user->familyName }}" required readonly>
            </div>
            <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
                @if ($errors->has('email'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
                @endif
                    <label class="Form-label is-required" for="email">
                        Indirizzo email istituzionale{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') ?? $user->email }}" required>
                    <p class="Form-message">
                        Inserisci la mail di lavoro fornita dalla tua PA (es. nome.cognome@agid.gov.it).{{-- //TODO: put message in lang file --}}
                    </p>
                @if ($errors->has('email'))
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
