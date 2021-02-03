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
                    <select class="form-select" aria-label="{{ __('Seleziona una credenziale OAuth2') }}" id="select-credential">
                        <option value="false" selected></option>
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
                            'credential' => '<strong>' . __('credenziale OAuth') . '</strong>'
                        ]) !!}
                    </p>
                @endif
            </div>
            <div class="col-md-6 col-12">
                <div class="text-center text-sm-right">
                    @component('layouts.components.link_button', [
                        'link' => $credentials,
                        'size' => 'lg',
                        'icon' => 'it-credential'
                    ])
                    {{ __('Gestione delle credenziali OAuth') }}
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
