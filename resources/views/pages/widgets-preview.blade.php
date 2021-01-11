@extends('layouts.page', ['graphicBackground' => true])

@section('title', 'Preview Widgets')

@section('content')
    <div class="row" id="widgets-preview-box" data-url="{{$url}}">
        <div class="col-lg-12 d-flex">
            @component('layouts.components.box')
                <div class="card-wrapper card-space">
                    <div class="card card-bg">
                        <div class="card-body">
                            <h5 class="card-title">Preview Widget</h5>
                            <p id="widget-subtitle-1"></p>
                            <div id="widget-preview" class="mt-4">
                                Nessun widget selezionato
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-wrapper card-space">
                    <div class="card card-bg">
                        <div class="card-body">
                            <h5 class="card-title">Codice HTML Widget</h5>
                            <p id="widget-subtitle-2"></p>
                            <code id="widget-code" class="mt-4">
                                Nessun widget selezionato 
                            </code>
                        </div>
                    </div>
                </div>
                <h3>Widget disponibili</h3>
                <p>Seleziona un widget per vederne la preview e ottenere il codice iframe da applicare nel sito
                    dell'amministrazione</p>
                <div class="row">
                    @foreach ($widgets as $key => &$widget)
                        <div class="col-4">
                            <div class="btn btn-block border rounded mb-4 text-center pointer" id="widget-{{ $key }}"
                                data-type="widget-select" data-attribute="back-to-top">
                                <span style="display: none;" id="widget-json-{{ $key }}"
                                    site="{{ $idSite }}">{{ json_encode($widget) }}</span>
                                <span class="align-middle" id="widget-child-text-{{ $key }}">{{ $widget['name'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endcomponent
        </div>
    </div>
@endsection
