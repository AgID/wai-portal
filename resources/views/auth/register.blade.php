@extends('layouts.page')

@section('title', __('ui.pages.auth.register.title'))

@section('page-content')
<form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('auth.register', [], false) }}">
    @csrf
    @if ($errors->isEmpty())
    <div class="Prose Alert Alert--info">
        <p class="u-text-p">Tutti i campi sono richiesti salvo dove espressamente indicato.{{-- //TODO: put message in lang file --}}</p>
    </div>
    @else
    <div class="Alert Alert--error Alert--withIcon u-margin-r-bottom" role="alert">
        <p class="u-text-p">
            È necessario correggere alcuni errori prima di poter inviare il modulo.{{-- //TODO: put message in lang file --}}
        </p>
    </div>
    @endif
    <fieldset class="Form-fieldset">
        <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @if ($errors->has('email'))
            <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                <p class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
            @endif
                <label class="Form-label is-required" for="email">
                    Indirizzo email istituzionale{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') }}" required/>
                <p class="Form-message">
                    Inserisci la mail di lavoro fornita dalla tua PA (es. nome.cognome@agid.gov.it).{{-- //TODO: put message in lang file --}}
                </p>
            @if ($errors->has('email'))
            </div>
            @endif
        </div>
    </fieldset>
    <fieldset class="Form-field Form-field--choose Grid-cell">
        <legend class="Form-legend is-required">
            Condizioni del servizio{{-- //TODO: put message in lang file --}}
        </legend>
        <label class="Form-label Form-label--block" for="accept_terms">
            <input type="checkbox" class="Form-input" id="accept_terms" name="accept_terms" aria-required="true" required/>
            <span class="Form-fieldIcon" role="presentation"></span>
            Accetto le condizioni del servizio{{-- //TODO: put message in lang file --}}
        </label>
    </fieldset>
    <div class="Form-field Grid-cell u-textCenter">
        <button type="submit" class="Button Button--default u-text-xs submit">
            Registra{{-- //TODO: put message in lang file --}}
        </button>
    </div>
</form>
@endsection
