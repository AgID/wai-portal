@extends('layouts.page')

@section('title', __('Analytics'))

@section('content')
<div class="container pt-3 pb-5">
    <div class="row">
        <div class="col-md-6 pr-5">
            <p class="lead font-weight-bold text-secondary text-serif">
                {{ __('Questo set di dati mostra la maniera in cui i cittadini interagiscono online con i siti web della tua pubblica amministrazione.') }}
                <br>
                <small class="font-italic">{{ __('I dati analytics mostrati si riferiscono agli ultimi 30 giorni (oggi escluso).') }}</small>
            </p>
        </div>
        <div class="col-md-6">
            @cannot(UserPermission::ACCESS_ADMIN_AREA)
            <div class="thick-border callout callout-highlight my-0 pl-4 primary-border-color-c1">
                <div class="callout-title">{{ __('dashboard') }}</div>
                <p class="text-sans-serif">
                    {{ __('Serve a consultare il dettaglio di tutti i dati e impostare opzioni personalizzate per il tracciamento.') }}
                    {{ __('Vuoi saperne di più?') }}
                    <a href="{{ route('faq') }}">{{ __('Consulta le FAQ') }}</a>
                </p>
                <a href="{{ route('analytics.service.login') }}" class="text-uppercase font-weight-bold text-decoration-none">
                    <svg class="icon icon-primary mr-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg#it-chart-line') }}"></use>
                    </svg>
                    {{ __('Vai alla dashboard') }}
                    <svg class="icon icon-primary ml-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
                    </svg>
                </a>
            </div>
            @endcannot
        </div>
    </div>
</div>
<div class="container py-3">
    @if ($publicAdministration->status->is(PublicAdministrationStatus::ACTIVE))
    <div class="row">
        @foreach($widgets as $widget)
        <div class="col-lg-{{ $widget['span'] ?? 4 }}">
            <div class="card-wrapper card-space">
                <div class="card card-bg">
                    <div class="card-body">
                        <h5 class="card-title big-heading">{{ $widget['title'][$locale] ?? $widget['title'][config('app.fallback_locale')] }}</h5>
                        <img id="spinner-widget-{{ $loop->index }}" class="icon mx-auto d-block" alt="Widget loading spinner" src="{{ asset('images/loading.svg') }}">
                        <iframe
                            id="widget-{{ $loop->index }}"
                            title="{{ $widget['title'][$locale] ?? $widget['title'][config('app.fallback_locale')] }}"
                            class="auto-resizeable  invisible"
                            sandbox="allow-same-origin allow-scripts"
                            src=""
                            data-src="{{ config('analytics-service.api_public_domain') }}{{ config('analytics-service.widgets_base_url') }}/{{ $widget['url'] }}&token_auth={{ $publicAdministration->token_auth }}&idSite={{ $publicAdministration->rollup_id }}&show_related_reports=0&language={{ $locale }}"
                            frameborder="0"
                            width="100%"
                            height="350"
                            marginheight="0"
                            marginwidth="0"
                            scrolling="no"></iframe>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        <script>
            api_public_domain = "{{ config('analytics-service.api_public_domain') }}";
            api_public_path = "{{ config('analytics-service.api_public_path') }}" || '';
        </script>
    </div>
    @else
    <div class="d-flex justify-content-center pb-5">
        <p class="text-serif text-center">
            {{ __('Sembra che la tua pubblica amministrazione non sia ancora attiva. Quando inizieremo a ricevere dati, in questa sezione potrai vedere un riepilogo di tutti i tuoi siti web.') }}
        </p>
    </div>
    @endif
</div>
@endsection
