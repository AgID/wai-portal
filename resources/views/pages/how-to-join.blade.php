@extends('layouts.page')

@section('title', __('Come partecipare'))

@section('content')
<div class="text-serif">
    @markdown(__("Per aderire a Web Analytics Italia e attivare le statistiche di uno o più
    siti di una PA è necessario che il referente di un'amministrazione presente su
    [IndicePA](https://www.indicepa.gov.it/) avvii la procedura di registrazione del
    sito istituzionale, accedendo alla piattaforma tramite una identità
    [SPID](https://www.spid.gov.it/)."))

    @markdown(__("Non ci sono particolari indicazioni sul referente ma è bene sapere
    che il Responsabile per la Transizione al Digitale (RTD) sarà tenuto informato
    rispetto alla registrazione della propria PA e all'aggiunta di nuovi siti web."))

    @markdown(__("L'adesione e l'uso delle funzionalità statistiche di WAI sono
    gratuite."))
</div>
<div class="thick-border callout callout-highlight note">
    <div class="callout-title">
        {{ __('Per iniziare hai bisogno di:') }}
    </div>
    <ul>
        <li>
            {{ __('credenziali SPID') }}
            <small>
                (<a href="https://www.spid.gov.it/richiedi-spid" class="external-link" rel="noopener noreferrer">{{ __('come faccio a ottenerle?') }}</a>)
            </small>
        </li>
        <li>
            {{ __('accesso al codice sorgente del sito') }}
        </li>
    </ul>
</div>
<div class="col-md-10 offset-1">
    <h2 class="section-header text-center">
        {{ __('ecco i passi da seguire') }}
    </h2>
    <ol class="how-to-join-steps">
        @foreach ($steps as $step)
        <li class="py-5">
            <div class="row flex-row{{ $loop->even ? '-reverse' : ''}}">
                <div class="col-md-6 d-flex align-items-center justify-content-center">
                    <img class="mx-5 h-100" src="{{ asset('images/how-to-join-steps/' . $step['image'] . '.svg') }}" alt="{{ $step['name'] }}">
                </div>
                <div class="col-md-6 step-content d-flex align-items-center">
                    <div>
                        <h3 class="step-title text-primary">{{ $step['name'] }}</h3>
                        @markdown($step['description'])
                        @isset($step['description_small'])
                        <small>@markdown($step['description_small'])</small>
                        @endisset
                    </div>
                </div>
            </div>
        </li>
        @endforeach
    </ol>
</div>
@endsection
