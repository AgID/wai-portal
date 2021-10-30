@extends('layouts.page', ['graphicBackground' => true])

@section('title', 'Anteprima widget')

@section('content')
    <div class="row" id="widgets-preview-box" data-url="{{ $apiPublicDomain }}{{ $widgetsBaseUrl }}">
        <div class="col-lg-12 d-flex">
            @component('layouts.components.box')
                <div class="card-wrapper card-space">
                    <div class="card card-bg no-after">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Anteprima') }}</h5>
                            <p class="widget-title"></p>
                            <div id="widget-preview" class="mt-4">
                                {{ __('Nessun widget selezionato') }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-wrapper card-space mb-5">
                    <div class="card card-bg no-after">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Codice HTML del widget') }}</h5>
                            <p class="widget-title"></p>
                            <code id="widget-code" class="mt-4">
                                {{ __('Nessun widget selezionato') }}
                            </code>
                            <div class="it-card-footer">
                                <div class="alert alert-info" role="alert">
                                    {{ __('Questo codice è utilizzabile esclusivamente nelle pagine web') }}
                                    @if (count($allowedFqdns) > 1)
                                        {{ __('sotto i seguenti domini:') }}
                                        <ul>
                                        @foreach ($allowedFqdns as $allowedFqdn)
                                            <li><code>{{ $allowedFqdn }}</code></li>
                                        @endforeach
                                        </ul>
                                    @else
                                        {!! __('sotto il dominio :fqdn.', [
                                            'fqdn' => '<code>' . head($allowedFqdns) . '</code>'
                                        ]) !!}
                                    @endif
                                    <br><br>
                                    {!! __("È possibile modificare i parametri in URL secondo le proprie esigenze,
                                        tuttavia si consiglia di mantentere l'impostazione predefinita per garantire
                                        la disponibilità dei dati.") !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h3>{{ __('Widget disponibili') }}</h3>
                <p>{{ __("Seleziona un widget per vederne l'anteprima e ottenere il codice HTML da inserire nel sito web.") }}</p>
                <div class="row">
                    @foreach ($allowedWidgets as $id => $widget)
                    @if (array_key_exists($id, $widgets))
                        <div class="col-md-4 mb-3">
                            <button type="button" class="btn btn-block rounded btn-outline-primary" id="widget-{{ $id }}"
                                data-type="widget-select" data-attribute="back-to-top" data-id-site="{{ $idSite }}"
                                data-widget-metadata='{{ json_encode($widgets[$id]) }}' data-widget-options='{{ json_encode($widget ?? []) }}'>
                                {{ $widgets[$id]['name'] }}
                            </button>
                        </div>
                    @endif
                    @endforeach
                </div>
            @endcomponent
        </div>
    </div>
@endsection
