@extends('layouts.home')

@section('title', __('Home'))

@section('after-header')
<div class="home-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 pb-5 pt-lg-5 px-3">
                <div class="text-white">
                    <h1 class="mb-0">{{ __('Le statistiche dei siti web') }}</h1>
                    <span class="payoff">{{ __('della pubblica amministrazione italiana') }}</span>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center align-items-center">
                <img class="img-fluid mt-auto" alt="" src="{{ asset('images/home-laptop.png') }}">
            </div>
        </div>
    </div>
    <div class="waves">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 213.97" preserveAspectRatio="xMidYMin slice">
            <style>
                .cls-1{fill:#fafcfd;opacity:0.64;}
                .cls-2{fill:#fff;}
            </style>
            <path class="cls-1" d="M1400,131.24V214H0V100.83q10.62-.52,21.44-.53C142.93,100.3,339.53,167,517.29,167S802.58,84,984.73,84Q1161.52,84,1400,131.24Z"/>
            <path class="cls-1" d="M1400,122.81V214H0V122.82q122.45-49,212-49c113.41,0,219,78.53,384.93,78.53S794.24,0,964.28,0Q1124.44,0,1400,122.81Z"/>
            <path class="cls-2" d="M1400,174.72V214H0V179q71.09-20.2,157.49-20.2c108.58,0,284.29,31.88,443.16,31.88S855.61,151,1018.41,151Q1180.27,151,1400,174.72Z"/>
        </svg>
        <a href="#dashboard" data-attribute="forward" class="home-forward shadow" aria-hidden="true">
            <svg class="icon icon-light"><use xlink:href="{{ asset('svg/sprite.svg#it-arrow-down') }}"></use></svg>
        </a>
    </div>
</div>
@endsection

@section('content')
<div class="home-content">
    <div class="bg-white py-5">
        <div class="container">
            <div class="row py-3">
                <div class="col-12 col-lg-4">
                    <div class="card-wrapper">
                        <div class="card">
                            <div class="card-body">
                            <h2 class="card-title big-heading">{{ __("Cos'è") }}</h2>
                            <p class="card-text">
                                {{ __(':app è una piattaforma che offre le statistiche in tempo reale dei visitatori dei siti della Pubblica Amministrazione, fornendo agli operatori dei report dettagliati.', ['app' => config('app.name')]) }}
                            </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card-wrapper">
                        <div class="card">
                            <div class="card-body">
                            <h2 class="card-title big-heading">{{ __("Come funziona") }}</h2>
                            <p class="card-text">
                                {{ __('I dati sono raccolti ed elaborati dalla piattaforma centralizzata :app (:app_short). :app_short ospita i dati statistici dei siti web delle PA italiane aderenti al progetto.', ['app' => config('app.name'), 'app_short' => config('app.name_short')]) }}
                            </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card-wrapper">
                        <div class="card">
                            <div class="card-body">
                            <h2 class="card-title big-heading">{{ __("A cosa serve") }}</h2>
                            <p class="card-text">
                                {{ __(":app_short aiuta le amministrazioni a comprendere il comportamento degli utenti online, con l'obiettivo di fornire ai cittadini siti e servizi via via più efficaci e inclusivi.", ['app_short' => config('app.name_short')]) }}
                            </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="dashboard">
        <div class="separator">
            <svg version="1.1" viewBox="0 0 1399 48" preserveAspectRatio="xMidYMax slice">
                <style type="text/css">
                    .st0{fill:#F7F9FA;}
                    .st1{stroke-opacity:0.5;fill-opacity:1;fill:#E3E4E6;stroke:#A8BDD9;}
                    .st2{opacity:0.5;fill:none;stroke:#A8BDD9;}
                    .st3{opacity:0.3;fill:none;stroke:#003E91;}
                </style>
                <polygon class="st0" points="1154.7,10 1011.3,16.4 895.4,36.2 775.4,16.4 670.6,42 548.3,16.4 447.4,42 326.1,10 150.9,42 0,16.4 0,48 1399,48 1399,16.4 1266.4,42 "/>
                <polyline class="st3" points="1399,16.4 1266.4,42 1154.7,10 1011.3,16.4 895.4,36.2 775.4,16.4 670.6,42 548.3,16.4 447.4,42 326.1,10 150.9,42 0,16.4 "/>
                <circle class="st1" cx="151" cy="42" r="5"/>
                <circle class="st1" cx="449" cy="42" r="5"/>
                <circle class="st1" cx="672" cy="42" r="5"/>
                <circle class="st1" cx="895" cy="37" r="5"/>
                <circle class="st1" cx="1013" cy="15" r="5"/>
                <circle class="st1" cx="1268" cy="42" r="5"/>
                <circle class="st1" cx="327" cy="10" r="5"/>
                <circle class="st2" cx="327" cy="10" r="9"/>
                <circle class="st1" cx="550" cy="15" r="5"/>
                <circle class="st2" cx="550" cy="15" r="9"/>
                <circle class="st1" cx="776" cy="17" r="5"/>
                <circle class="st2" cx="776" cy="17" r="9"/>
                <circle class="st1" cx="1155" cy="10" r="5"/>
                <circle class="st2" cx="1155" cy="10" r="9"/>
        </svg>
        </div>
        <div class="lightgrey-bg-c1 py-5">
            <div class="container py-3">
                <h2 class="display-1 text-center">{{ __('Analytics') }}</h2>
                <div class="d-flex justify-content-center pb-5">
                    <p class="text-serif text-center">
                        {{ __('Questa sezione espone alcuni set di dati che mostrano la maniera in cui i cittadini interagiscono online con i siti web della pubblica amministrazione italiana.') }}
                        <br>
                        <span class="font-italic">{{ __('I dati analytics mostrati si riferiscono agli ultimi 30 giorni (oggi escluso).') }}</span>
                    </p>
                </div>
                <div class="row">
                    <div class="col-lg">
                        <div class="card-wrapper card-space">
                            <div class="card card-bg">
                                <div class="card-body">
                                    <h2 class="card-title big-heading">{{ __('Riepilogo portale') }}</h2>
                                    @include('layouts.includes.portal_widget', ['publicAdministrationsCounter' => $publicAdministrationsCount, 'websitesCounter' => $websitesCount])
                                </div>
                            </div>
                        </div>
                    </div>
                    @foreach($widgets as $widget)
                    <div class="col-lg-{{ $widget['span'] ?? 4 }}">
                        <div class="card-wrapper card-space">
                            <div class="card card-bg">
                                <div class="card-body">
                                    <h2 class="card-title big-heading">{{ $widget['title'][$locale] ?? $widget['title'][config('app.fallback_locale')] }}</h2>
                                    <img id="spinner-widget-{{ $loop->index }}" class="icon mx-auto d-block" alt="Widget loading spinner" src="{{ asset('images/loading.svg') }}">
                                    <iframe
                                        id="widget-{{ $loop->index }}"
                                        title="{{ $widget['title'][$locale] ?? $widget['title'][config('app.fallback_locale')] }}"
                                        src=""
                                        class="auto-resizeable invisible"
                                        sandbox="allow-same-origin allow-scripts"
                                        data-src="{{ config('analytics-service.api_public_domain') }}{{ config('analytics-service.widgets_base_url') }}/{{ $widget['url'] }}&idSite={{ config('analytics-service.public_dashboard') }}&show_footer_icons=0&show_related_reports=0&language={{ $locale }}"
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
            </div>
        </div>
    </div>
    {{-- <div class="py-5">
        <div class="container py-3">
            <h3 class="text-center">{{ __('Come iniziare a tracciare il traffico') }}</h3>
            <div class="d-flex justify-content-center pb-5">
                <p class="text-serif text-center">
                    {!! __('Il progetto :app si trova attualmente in fase di :phase (:phase-description).', ['app' => config('app.name'), 'phase' => '<em>' . __('closed-beta') . '</em>', 'phase-description' => __('versione beta chiusa limitata ad un panel ristretto di amministrazioni')]) !!}
                </p>
            </div>
            <div class="row">
                <div class="col-lg-6 p-4 p-lg-5 border-bottom border-lg-bottom-0 border-lg-right d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="section-header long-dash">{{ __('amministrazioni pilota') }}</h5>
                        <p class="text-serif mb-5">
                            {{ __('Se vuoi che la tua amministrazioni sia tra le prime ad utilizzare la piattaforma contattaci per aderire alla fase sperimentale.') }}
                        </p>
                    </div>
                    <div>
                        @component('layouts.components.link_button', [
                            'link' => route('contacts'),
                            'type' => 'outline-primary',
                        ])
                        {{ __('contattaci') }}
                        @endcomponent
                    </div>
                </div>
                <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="section-header long-dash">{{ __('accesso alla piattaforma') }}</h5>
                        <p class="text-serif">
                            @guest
                            {{ __('Accedi con le tue credenziali SPID e registra la tua PA su :app.', ['app' => config('app.name')]) }}
                            <br>
                            <a href="{{ route('faq') }}">{{ __('Maggiori informazioni.') }}</a>
                            @else
                            {{ __("Hai già effettuato l'accesso a :app.", ['app' => config('app.name')]) }}
                            <br>
                            {!! __('Consulta i dati analytics e prendi :data_driven_decisions per la tua PA.', ['data_driven_decisions' => '<em>' . __('decisioni basate sui dati') . '</em>']) !!}
                            @endguest
                        </p>
                    </div>
                    @guest
                    <p>{!! __("Usa il bottone 'Entra con SPID' che trovi :back-to-top a destra nella pagina.", ['back-to-top' => '<a href="#" data-attribute="back-to-top">' . __('in alto') . '</a>']) !!}</p>
                    @else
                    <div>
                    @component('layouts.components.link_button', [
                        'link' => route('analytics'),
                        'type' => 'outline-primary',
                        'icon' => 'it-arrow-right',
                        'iconColor' => 'primary',
                    ])
                    {{ __('vai agli analytics') }}
                    @endcomponent
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </div> --}}
    <div class="container py-3">
        <div class="row">
            @component('layouts.components.box', ['classes' => 'p-5 up-xs-shadow'])
            <h2 class="text-center mb-5">{{ __('Con :app puoi', ['app' => config('app.name')]) }}</h2>
            <ul class="text-serif">
                <li>
                    <p class="lead mw-100">
                        {{ __('monitorare gratuitamente le statistiche dei siti della tua PA') }}
                    </p>
                </li>
                <li>
                    <p class="lead mw-100">
                        {{ __('capire come migliorare la fruizione delle tue pagine web e dei tuoi servizi digitali') }}
                    </p>
                </li>
                <li>
                    <p class="lead mw-100">
                        {{ __('avere la proprietà, il controllo completo dei dati e la piena aderenza al GDPR') }}
                    </p>
                </li>
                <li>
                    <p class="lead mw-100">
                        {!! __('beneficiare di strumenti ad hoc per pubblicare le statistiche dei siti monitorati (:art-7-cad) e per condividere i dati con i decisori interni', [
                            'art-7-cad' => '<a href="https://docs.italia.it/italia/piano-triennale-ict/codice-amministrazione-digitale-docs/it/v2018-09-28/_rst/capo1_sezione2_art7.html" class="external-link" target="_blank" rel="noopener noreferrer">art. 7 CAD</a>'
                        ]) !!}
                    </p>
                </li>
            </ul>
            @endcomponent
        </div>
    </div>
    <div class="bg-primary py-5 position-relative">
        <div class="container text-center text-white py-3">
            <img alt="{{ config('app.name') }} - logo" class="icon icon-xl" src="{{ asset(config('site.logo')) }}">
            <h3 class="my-4">{{ __('Hai bisogno di aiuto?') }}</h3>
            <div class="d-flex justify-content-center py-3">
                <p class="text-serif text-center">
                    {{ __('Se hai dei dubbi su come funziona la piattaforma :app_short o su come fare per integrarla per la tua amministrazione', ['app_short' => config('app.name_short')]) }}
                </p>
            </div>
            @component('layouts.components.link_button', [
                'link' => route('faq'),
            ])
            {{ __('Consulta le FAQ') }}
            @endcomponent
        </div>
        <div class="absolute-top-left d-none d-md-block">
            <div class="graphic-container v-flip">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 260 220.91" preserveAspectRatio="xMidYMax meet">
                    <path style="fill:#fff;fill-rule:evenodd;opacity:0.06;" d="M230,0a30,30,0,0,0-30,30V220.91h60V30A30,30,0,0,0,230,0ZM30,60A30,30,0,0,0,0,90V220.91H60V90A30,30,0,0,0,30,60ZM130,45a30,30,0,0,0-30,30V220.91h60V75A30,30,0,0,0,130,45Z"/>
                </svg>
            </div>
        </div>
        <div class="absolute-bottom-right d-none d-md-block mr-5">
            <div class="graphic-container">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 260 220.91" preserveAspectRatio="xMidYMax meet">
                    <path style="fill:#fff;fill-rule:evenodd;opacity:0.06;" d="M230,0a30,30,0,0,0-30,30V220.91h60V30A30,30,0,0,0,230,0ZM30,60A30,30,0,0,0,0,90V220.91H60V90A30,30,0,0,0,30,60ZM130,45a30,30,0,0,0-30,30V220.91h60V75A30,30,0,0,0,130,45Z"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection
