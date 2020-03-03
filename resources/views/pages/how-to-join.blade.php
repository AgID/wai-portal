@extends('layouts.page')

@section('title', __('Come partecipare'))

@section('content')
<div class="text-serif">
    @markdown(__("Per aderire a :app e attivare le statistiche di uno o più
    siti di una PA è necessario che un referente di un'amministrazione presente
    su [IndicePA](https://www.indicepa.gov.it/) avvii la procedura di
    registrazione del sito istituzionale, accedendo alla piattaforma tramite una
    identità [SPID](https://www.spid.gov.it/).", ['app' => config('app.name')]))

    @markdown(__("Non ci sono particolari indicazioni sul referente ma è bene
    sapere che il Responsabile per la Transizione al Digitale (RTD) sarà tenuto
    informato rispetto alla registrazione della propria PA e all'aggiunta di
    nuovi siti web."))

    @markdown(__("L'adesione e l'uso delle funzionalità statistiche di :app sono
    gratuite e [compatibili con qualunque altra modalità di raccolta di dati
    analytics](/faq#compatibilita-altri-script-tracciamento).",
    ['app' => config('app.name_short')]))
</div>
<div class="thick-border callout callout-highlight note">
    <div class="callout-title">
        {{ __('Per iniziare hai bisogno di:') }}
    </div>
    <ul>
        <li>
            {{ __('credenziali SPID') }}
            <small>
                (<a href="https://www.spid.gov.it/richiedi-spid" class="external-link" target="_blank" rel="noopener noreferrer">{{ __('come faccio a ottenerle?') }}</a>)
            </small>
        </li>
        <li>
            {{ __('accesso completo al sito (o al suo codice sorgente)') }}
        </li>
    </ul>
</div>
<div class="col-lg-10 offset-lg-1">
    <h2 class="section-header text-center my-5">
        {{ __('ecco i passi da seguire') }}
    </h2>
    <ol class="how-to-join-steps p-0">
        @foreach ($steps as $step)
        <li class="pb-5 py-md-5">
            <div class="row flex-md-row{{ $loop->odd ? '-reverse' : ''}}">
                <div class="col-md-6 step-content d-flex align-items-center">
                    <div>
                        <h3 class="step-title text-primary">{{ $step['name'] }}</h3>
                        @markdown($step['description'])
                        @isset($step['description_small'])
                        <small>@markdown($step['description_small'])</small>
                        @endisset
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img class="img-fluid mx-5 h-100" src="{{ asset('images/how-to-join-steps/' . $step['image'] . '.svg') }}" alt="{{ $step['name'] }}">
                </div>
            </div>
        </li>
        @endforeach
    </ol>
    <h4 id="video" class="section-header text-center my-5">
        {{ __('guarda la procedura in questo breve video') }}
    </h4>
    <div class="embed-responsive embed-responsive-16by9 mb-5">
        <iframe title="{{ __('WAI - Registrazione sito istituzionale') }}" class="embed-responsive-item" width="560" height="315" src="https://www.youtube.com/embed/jq7ycSLQE2k?rel=0&modestbranding=1" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>
@endsection
