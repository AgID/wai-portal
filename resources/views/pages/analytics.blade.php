@extends('layouts.page')

@section('title', __('Analytics'))

@section('content')
<div class="container pt-3 pb-5">
    <div class="row">
        <div class="col-md-6 pr-5">
            <p class="lead font-weight-bold text-secondary text-serif">
                {{ __('Questo set di dati mostra la maniera in cui i cittadini interagiscono online con i siti web della tua pubblica amministrazione che hai inserito sulla piattaforma.') }}
            </p>
        </div>
        <div class="col-md-6">
            @cannot(UserPermission::ACCESS_ADMIN_AREA)
            <div class="go-to-dashboard callout callout-highlight my-0 pl-4 primary-border-color-c1">
                <div class="callout-title">{{ __('dashboard') }}</div>
                <p class="text-sans-serif">
                    {{ __('Serve a consultare il dettaglio di tutti i dati e impostare opzioni personalizzate per il tracciamento.') }}
                    {{ __('Vuoi saperne di più?') }}
                    <a href={{ route('faq') }}>{{ __('Consulta le FAQ') }}</a>
                </p>
                <a href="{{ route('analytics.service.login') }}" class="text-uppercase font-weight-bold text-decoration-none">
                    <svg class="icon icon-primary mr-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg') }}#it-chart-line"></use>
                    </svg>
                    {{ __('Vai alla dashboard') }}
                    <svg class="icon icon-primary ml-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg') }}#it-arrow-right"></use>
                    </svg>
                </a>
            </div>
            @endcannot
        </div>
    </div>
</div>
<div class="container py-3">
    <div class="row">
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
