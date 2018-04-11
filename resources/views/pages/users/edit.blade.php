@extends('layouts.default')

@section('title', __('ui.pages.users.add.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('users-update', ['user' => $user], false) }}">
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
            <legend class="Form-legend">
                Informazioni dell'utente{{-- //TODO: put message in lang file --}}
            </legend>
            <div class="Form-field {{ $errors->has('name') ? 'is-invalid' : '' }}">
                @if ($errors->has('name'))
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('name') }}</p>
                        @endif
                        <label class="Form-label is-required" for="name">
                            Nome{{-- //TODO: put message in lang file --}}
                        </label>
                        <input class="Form-input is-disabled" id="name" name="name" type="text" aria-required="true" value="{{ $user->name }}" required readonly>
                        @if ($errors->has('name'))
                    </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('familyName') ? 'is-invalid' : '' }}">
                @if ($errors->has('familyName'))
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('familyName') }}</p>
                        @endif
                        <label class="Form-label is-required" for="familyName">
                            Cognome{{-- //TODO: put message in lang file --}}
                        </label>
                        <input class="Form-input is-disabled" id="familyName" name="familyName" type="text" aria-required="true" value="{{ $user->familyName }}" required readonly>
                        @if ($errors->has('familyName'))
                    </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
                @if ($errors->has('email'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
                @endif
                    <label class="Form-label is-required" for="email">
                        Indirizzo email istituzionale{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ $user->email }}" required readonly>
                    <p class="Form-message">
                        Inserisci la mail di lavoro fornita dalla tua PA (es. nome.cognome@agid.gov.it).{{-- //TODO: put message in lang file --}}
                    </p>
                @if ($errors->has('email'))
                </div>
                @endif
            </div>
            <div class="Form-field {{ $errors->has('role') ? 'is-invalid' : '' }}">
                @if ($errors->has('role'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('role') }}</p>
                @endif
                    <label class="Form-label is-required" for="role">
                        Ruolo
                        <small>(vedi descrizione)</small>{{-- //TODO: put message in lang file --}}
                    </label>
                    <select class="Form-input" id="role" name="role" aria-required="true" required>
                        <option value="" selected disabled>seleziona</option>{{-- //TODO: put message in lang file --}}
                        <option value="reader" {{ old('role') ?? $user->roles()->first()->name == 'reader' ? "selected" : "" }}>{{ __('auth.roles.reader') }}</option>
                        <option value="manager" {{ old('role') ?? $user->roles()->first()->name == 'manager' ? "selected" : "" }}>{{ __('auth.roles.manager') }}</option>
                        <option value="admin" {{ old('role') ?? $user->roles()->first()->name == 'admin' ? "selected" : "" }}>{{ __('auth.roles.admin') }}</option>
                    </select>
                @if ($errors->has('role'))
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
    <h4 class="u-text-h4 u-border-bottom-xxs">
        Descrizione dei ruoli{{-- //TODO: put message in lang file --}}
    </h4>
    <dl class="DescriptionList">
        <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.reader')) }}</dt>
        <dd class="DescriptionList-Description">{{ __('auth.roles.reader_description') }}</dd>
        <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.manager')) }}</dt>
        <dd class="DescriptionList-Description">{{ __('auth.roles.manager_description') }}</dd>
        <dt class="DescriptionList-Term">{{ ucfirst(__('auth.roles.admin')) }}</dt>
        <dd class="DescriptionList-Description">{{ __('auth.roles.admin_description') }}</dd>
    </dl>
@endsection
