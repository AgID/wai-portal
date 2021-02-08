@extends('layouts.page', ['graphicBackground' => true])

@section('title', 'API')

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        <div class="row">
            <div class="col-md-6 col-12">
                @if ($hascredentials)
                <div class="bootstrap-select-wrapper">
                    <label>{{ __('Seleziona una credenziale da usare per le chiamate API di prova.') }}</label>
                    <select title="{{ __('Seleziona una credenziale') }}" class="form-select" id="select-credential">
                        @foreach ($credentialsList as $index => &$credential)
                            <option value="{{ $credential->consumer_id }}">
                                {{ $credential->client_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                    <p>
                        {!! __('Se vuoi provare le API è necessario prima aggiungere una :credential.', [
                            'credential' => '<strong>' . __('credenziale') . '</strong>'
                        ]) !!}
                    </p>
                @endif
            </div>
            <div class="col-md-6 col-12">
                <div class="text-center text-sm-right">
                    @component('layouts.components.link_button', [
                        'link' => $credentials,
                        'size' => 'lg',
                        'icon' => 'it-key'
                    ])
                    {{ __('Gestione delle credenziali API') }}
                    @endcomponent
                </div>
            </div>
        </div>

        @if (!$production)
            <div id="swagger-ui" data-url="{{ $apiUrl }}" data-environment={{ $production }}/>
        @endif

        @endcomponent
    </div>
</div>
@endsection
