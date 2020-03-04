@extends('layouts.page', ['graphicBackground' => true])

@section('title', $user->full_name)

@section('content')
    <div class="row">
        <div class="col-lg-8 d-flex">
            @component('layouts.components.box')
            <h4 class="text-uppercase m-0">{{ __('Anagrafica') }}</h4>
            <div class="mt-5 pt-5">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control-plaintext" id="name" value="{{ $user->name ?? __('Non ancora disponibile') }}" readonly>
                        <label for="name">{{ __('Nome') }}</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control-plaintext" id="family_name" value="{{ $user->family_name ?? __('Non ancora disponibile') }}" readonly>
                        <label for="family_name">{{ __('Cognome') }}</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control-plaintext" id="fiscal_number" value="{{ $user->fiscal_number }}" readonly>
                        <label for="fiscal_number">{{ __('Codice fiscale') }}</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control-plaintext" id="email" value="{{ $user->email }}" readonly>
                        <label for="email">{{ __('Indirizzo email') }}</label>
                    </div>
                </div>
                @can(UserPermission::MANAGE_USERS)
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control-plaintext" id="created_at" value="{{ $user->created_at->format('d/m/Y') }}" readonly>
                        <label for="created_at">{{ __('Aggiunto il') }}</label>
                    </div>
                    @isset($user->updated_at)
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control-plaintext" id="type" value="{{ $user->updated_at->format('d/m/Y') }}" readonly>
                        <label for="type">{{ __('Aggiornato il') }}</label>
                    </div>
                    @endisset
                    @isset($user->last_access_at)
                    <div class="form-group col-md-4">
                        <input type="text" class="form-control-plaintext" id="type" value="{{ $user->last_access_at->format('d/m/Y H:i') }}" readonly>
                        <label for="type">{{ __('Ultimo accesso') }}</label>
                    </div>
                    @endisset
                </div>
                @component('layouts.components.link_button', [
                    'icon' => 'it-pencil',
                    'link' => $userEditUrl,
                    'size' => 'lg',
                ])
                {{ ucfirst(__('modifica')) }}
                @endcomponent
                @endcan
            </div>
            @endcomponent
        </div>
        <div class="col-lg-4 d-flex">
            @component('layouts.components.box', ['classes' => 'd-flex flex-column justify-content-between'])
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="text-uppercase m-0">{{ __('stato') }}</h4>
                <span class="badge px-3 py-2 user-status {{ strtolower($user->status->key) }}">
                    {{ strtoupper($user->status->description) }}
                </span>
            </div>
            <div class="box-section lightgrey-bg-a1 my-4">
                <p>{{ UserStatus::getLongDescription($user->status) }}</p>
                @can(UserPermission::MANAGE_USERS)
                @if ($user->status->is(UserStatus::INVITED))
                <h5 class="section-header">{{ __('invito') }}</h5>
                <p>
                    {{ __("Se l'indirizzo email è corretto puoi inviare un nuovo invito!") }}
                </p>
                <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                    href="{{ $userVerificationResendUrl }}"
                    data-type="verificationResend"
                    data-email="{{ $user->email }}"
                    data-ajax
                    aria-disabled="true">
                    {{ __('Rispedisci invito') }}
                </a>
                <p class="mt-3 font-weight-semibold">
                    {{ __("Se non accetta l'invito, l'utente sarà rimosso fra :purgeDays.",[
                        'purgeDays' => trans_choice(
                                "{1} :count giorno|[2,*] :count giorni",
                                (int) config('auth.verification.purge') - $user->created_at->diffInDays(now())
                            )
                    ]) }}
                </p>
                @endif
                @if ($user->status->is(UserStatus::ACTIVE) && !$user->is($authUser))
                <h5 class="section-header">{{ __('sospensione') }}</h5>
                <p>
                    {{ __("Se vuoi impedire l'accesso a questo utente puoi sospenderlo.") }}
                </p>
                <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                    href="{{ $userSuspendUrl }}"
                    data-type="userSuspendReactivate"
                    data-user-name="{{ $user->name }}"
                    data-current-status-description="{{ $user->status->description }}"
                    data-current-status="{{ $user->status->key }}"
                    aria-disabled="true">
                    {{ ucfirst(__('sospendi')) }}
                </a>
                @endif
                @if ($user->status->is(UserStatus::SUSPENDED))
                <h5 class="section-header">{{ __('riattivazione') }}</h5>
                <p>
                    {{ __("Se vuoi di nuovo consentire l'accesso a questo utente puoi riattivarlo.") }}
                </p>
                <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                    href="{{ $userReactivateUrl }}"
                    data-type="userSuspendReactivate"
                    data-user-name="{{ $user->name }}"
                    data-current-status-description="{{ $user->status->description }}"
                    data-current-status="{{ $user->status->key }}"
                    aria-disabled="true">
                    {{ ucfirst(__('riattiva')) }}
                </a>
                @endif
                @if ($user->status->is(UserStatus::PENDING))
                <h5 class="section-header">{{ __('non attivo') }}</h5>
                <p>
                    {{ __("L'attivazione dell'utente avviene quando il sito istituzionale della PA inizia a tracciare il traffico.") }}
                </p>
                <p>
                    {{ __('Non è possibile modificare lo stato.') }}
                </p>
                @endif
                @endcan
            </div>
            <div>
                <p class="text-serif">
                    {{ __("Hai dubbi sul significato dello stato dell'utente?") }}
                    <a href="{{ route('faq') }}">{{ __('Consulta le FAQ') }}</a>
                </p>
            </div>
            @endcomponent
        </div>
    </div>
    @can(UserPermission::MANAGE_WEBSITES)
    @component('layouts.components.box', ['classes' => 'mt-0'])
    <h4 class="text-uppercase mb-5">{{ __('ruolo e permessi sui siti web') }}</h4>
    <div class="box-section lightgrey-bg-a1 my-4 py-5">
        <h5 class="section-header mb-4">{{ __('ruolo') }}</h5>
        <div class="row">
            @foreach ($allRoles as $role)
            <div class="col-md-4 d-flex align-items-center">
                <span class="badge user-role {{ $role['name'] }}">{{ $role['description'] }}</span>
            </div>
            <div class="col-md-8">
                <p class="mb-0">
                    <svg class="icon"><use xlink:href="{{ asset('svg/sprite.svg#it-info-circle') }}"></use></svg>
                    {{ $role['longDescription'] }}
                </p>
            </div>
            <div class="col">
                <p class="mt-5 mb-0 text-serif">
                    {{ __("Vuoi saperne di più su ruoli e permessi?") }}
                    <a class="external-link" rel="noopener noreferrer" href="{{ config('site.kb.link') }}utenti/inserimento.html">
                        {{ __('Leggi la guida') }}
                    </a>
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @include('partials.datatable')
    <div class="mt-4 text-center text-sm-left">
    @component('layouts.components.link_button', [
        'icon' => 'it-pencil',
        'link' => $userEditUrl,
        'size' => 'lg',
    ])
    {{ ucfirst(__('modifica')) }}
    @endcomponent
    </div>
    @endcomponent
    @endcan
    </div>
@endsection
