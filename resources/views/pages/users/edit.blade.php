@extends('layouts.default')

@section('title', __('ui.pages.users.edit.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? route('admin.publicAdministration.users.update', ['publicAdministration' => request()->route('publicAdministration'), 'user' => $user], false) : route('users.update', ['user' => $user], false) }}">
        @csrf
        @method('PATCH')
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
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ $user->email }}" required>
                    <p class="Form-message">
                        Inserisci la mail di lavoro fornita dalla tua PA (es. nome.cognome@agid.gov.it).{{-- //TODO: put message in lang file --}}
                    </p>
                @if ($errors->has('email'))
                </div>
                @endif
            </div>
            @include('partials.user_full_website_permissions', ['admin' => $admin])
        </fieldset>
        <div class="Form-field Grid-cell u-textRight">
            <button type="submit" class="Button Button--default u-text-xs">
                Invia{{-- //TODO: put message in lang file --}}
            </button>
        </div>
    </form>
@endsection
