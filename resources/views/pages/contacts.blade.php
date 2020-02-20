@extends('layouts.page')

@section('title', __('Contattaci'))

@section('content')
<div class="row py-5">
    <div class="col-12 col-lg-6">
        <div class="card-wrapper card-space">
            <div class="card card-bg card-big">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between top-icon">
                        <h5 class="card-title section-header long-dash m-0">{{ __('utenti di :app', ['app' => config('app.name_short')]) }}</h5>
                        <svg class="icon flex-shrink-0">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use>
                        </svg>
                    </div>
                    <p class="card-text text-serif">
                        {{ __('Se devi darci un feedback oppure inoltrare una richiesta puoi usare il componente che appare sulla destra in ogni pagina.') }}
                    </p>
                    <p class="card-text text-serif">
                        {!! __('Ricorda che è anche disponibile la :user-guide di :app su Docs Italia.', [
                            'user-guide' => implode([
                                '<a class="external-link" rel="noopener noreferrer" href="https://docs.italia.it/agid/wai/wai-user-guide-docs/it/stabile/">',
                                __('guida utente'),
                                '</a>',
                            ]),
                            'app' => config('app.name_short'),
                        ]) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card-wrapper card-space">
            <div class="card card-bg card-big">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between top-icon">
                        <h5 class="card-title section-header long-dash m-0">{{ __('Responsabili per la transizione al digitale') }}</h5>
                        <svg class="icon flex-shrink-0">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use>
                        </svg>
                    </div>
                    <p class="card-text text-serif">
                        {!! __('Mettiamoci in contatto usando :rete-digitale, lo strumento di collaborazione riservato agli RTD.', [
                            'rete-digitale' => '<a class="external-link" rel="noopener noreferrer" href="https://agid.eu.brightidea.com/">' . __('ReTe Digitale') . '</a>'
                        ]) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card-wrapper card-space">
            <div class="card card-bg card-big">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between top-icon">
                        <h5 class="card-title section-header long-dash m-0">{{ __('sviluppatori') }}</h5>
                        <svg class="icon flex-shrink-0">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-code-circle') }}"></use>
                        </svg>
                    </div>
                    <p class="card-text text-serif">
                        {!! __('Puoi entrare in contatto con gli sviluppatori di :app usando i nostri
                        respository su GitHub.', [
                            'app' => '<span class="font-weight-semibold">' . config('app.name') . '</span>'
                        ]) !!}
                    </p>
                    <div class="it-list-wrapper">
                        <ul class="it-list">
                            <li>
                                <a href="http://github.com/agid/wai-portal" rel="noopener noreferrer" target="_blank">
                                    <div class="it-rounded-icon">
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('svg/sprite.svg#it-github') }}"></use>
                                        </svg>
                                    </div>
                                    <div class="it-right-zone">
                                        <span class="text">
                                            {{ __('Stack applicativo') }}
                                        </span>
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('svg/sprite.svg#it-external-link') }}"></use>
                                        </svg>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="http://github.com/agid/wai-infrastructure" rel="noopener noreferrer" target="_blank">
                                    <div class="it-rounded-icon">
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('svg/sprite.svg#it-github') }}"></use>
                                        </svg>
                                    </div>
                                    <div class="it-right-zone">
                                        <span class="text">
                                            {{ __('Stack infrastrutturale') }}
                                        </span>
                                        <svg class="icon">
                                            <use xlink:href="{{ asset('svg/sprite.svg#it-external-link') }}"></use>
                                        </svg>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card-wrapper card-space">
            <div class="card card-bg card-big">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between top-icon">
                        <h5 class="card-title section-header long-dash m-0">{{ __('tutti gli altri') }}</h5>
                        <svg class="icon flex-shrink-0">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-mail') }}"></use>
                        </svg>
                    </div>
                    <p class="card-text text-serif">
                        {!! __("Se non hai un altro modo per contattarci, scrivi una mail alla casella :mail.", [
                            'mail' => '<span class="text-monospace">' . config('site.owner.mail') . '</span>'
                        ]) !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
