@extends('layouts.page', ['fullWidth' => true])

@section('title', $user->full_name)

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        <div class="row">
            <div class="col-md-8 d-flex">
                @component('layouts.components.box')
                <h4 class="text-uppercase m-0">{{ __('anagrafica') }}</h4>
                <div class="mt-5 pt-5">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="name" value="{{ $user->name }}" readonly>
                            <label for="name">{{ __('Nome') }}</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="family_name" value="{{ $user->family_name }}" readonly>
                            <label for="family_name">{{ __('Cognome') }}</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="email" value="{{ $user->email }}" readonly>
                            <label for="email">{{ __('Indirizzo email') }}</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="created_at" value="{{ $user->created_at->format('d/m/Y') }}" readonly>
                            <label for="created_at">{{ __('Aggiunto il') }}</label>
                        </div>
                    </div>
                    <div class="form-row">
                        @isset($user->updated_at)
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="type" value="{{ $user->updated_at->format('d/m/Y') }}" readonly>
                            <label for="type">{{ __('Aggiornato il') }}</label>
                        </div>
                        @endisset
                        @isset($user->last_access_at)
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="type" value="{{ $user->last_access_at->format('d/m/Y H:i') }}" readonly>
                            <label for="type">{{ __('Ultimo accesso') }}</label>
                        </div>
                        @endisset
                    </div>
                    @include('partials.link_button', [
                        'label' => __('Modifica'),
                        'icon' => 'it-pencil',
                        'link' => route('admin.users.edit', ['user' => $user]),
                        'size' => 'lg',
                    ])
                </div>
                @endcomponent
            </div>
            <div class="col-md-4 d-flex">
                @component('layouts.components.box')
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="text-uppercase m-0">{{ __('stato') }}</h4>
                    <span class="badge px-3 py-2 user-status {{ strtolower($user->status->key) }}">
                        {{ strtoupper($user->status->description) }}
                    </span>
                </div>
                <div class="box-section lightgrey-bg-a1 my-4">
                    <p>{{ UserStatus::getLongDescription($user->status) }}</p>
                    @if ($user->status->is(UserStatus::INVITED))
                    <h5 class="section-header">{{ __('invito') }}</h5>
                    <p>
                        {{ __("Se l'indirizzo email è corretto puoi inviare di nuovo l'invito!") }}
                    </p>
                    <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                        href="{{ route('admin.users.verification.resend', ['user' => $user]) }}"
                        data-type="verificationResend"
                        data-email="{{ $user->email }}"
                        data-ajax
                        aria-disabled="true">
                        {{ __('Rispedisci invito') }}
                    </a>
                    @endif
                    @if ($user->status->is(UserStatus::ACTIVE))
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
                        {{ __('Sospendi') }}
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
                        {{ __('Riattiva') }}
                    </a>
                    @endif
                </div>
                <div>
                    <p class="text-serif">
                        {{ __("Hai dubbi sul significato dello stato dell'utente?") }}
                        <a href={{ route('faq') }}>{{ __('Consulta le FAQ') }}</a>
                    </p>
                </div>
                @endcomponent
            </div>
        </div>
        @component('layouts.components.box', ['classes' => 'mt-0'])
        <h4 class="text-uppercase mb-5">{{ __('ruolo') }}</h4>
        <div class="box-section lightgrey-bg-a1 my-4 py-5">
            <div class="row">
                <div class="col-md-4 d-flex align-items-center">
                    <span class="badge user-role {{ UserRole::SUPER_ADMIN }}">{{ UserRole::getDescription(UserRole::SUPER_ADMIN) }}</span>
                </div>
                <div class="col-md-8">
                    <p class="mb-0">
                        <svg class="icon"><use xlink:href="{{ asset('svg/sprite.svg#it-info-circle') }}"></use></svg>
                        {{ UserRole::getLongDescription(UserRole::SUPER_ADMIN) }}
                    </p>
                </div>
                <div class="col">
                    <p class="mt-5 mb-0 text-serif">
                        {{ __("Hai dubbi sul significato del ruolo dell'utente?") }}
                        <a href={{ route('faq') }}>{{ __('Consulta le FAQ') }}</a>
                    </p>
                </div>
            </div>
        </div>
        @endcomponent
    </div>
</div>
@endsection
