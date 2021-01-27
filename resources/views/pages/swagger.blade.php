@extends('layouts.page', ['graphicBackground' => true])

@section('title', "API swagger")

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        <div class="row">
            <div class="col-md-6 col-12">
                @if ($haskeys)
                <div class="bootstrap-select-wrapper">
                    <label>Seleziona una chiave da usare con swagger</label>
                    <select class="form-select" aria-label="Seleziona una chiave OAuth2" id="select-key">
                        @foreach ($keysList as $index => &$key)
                            <option value="{{$key->consumer_id}}" {{$index === 0 ? "selected" : ""}}>{{$key->client_name}}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" class="btn btn-outline-primary mt-4" id="use-key">Conferma</button>
                @else
                    <p class="text-center">
                        {{ __('Per accedere alla documentazione ') }}
                        <strong>{{__('API swagger')}}</strong> 
                        {{__('è necessario aggiungere una ')}}
                        <strong>{{__('chiave OAuth')}}</strong>
                    </p>
                @endif
            </div>
            <div class="col-md-6 col-12">
                <div class="text-center text-sm-right">
                    @component('layouts.components.link_button', [
                        'link' => $keys,
                        'size' => 'lg',
                        'icon' => 'it-key'
                    ])
                    {{ ucfirst(__('Gestione delle credenziali OAuth')) }}
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