@extends('layouts.page', ['graphicBackground' => true])

@section('title', $key->client_name)

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
            <h4 class="text-uppercase m-0">{{ __('Informazioni Chiave') }}</h4>
            <div class="mt-5 pt-5">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input 
                        type="text" 
                        class="form-control-plaintext" 
                        id="name" 
                        value="{{ $key->client_name ?? __('Non ancora disponibile') }}" 
                        readonly
                        >
                        <label for="name">{{ __('Nome') }}</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input 
                        type="text" 
                        class="form-control-plaintext" 
                        id="name" 
                        value="{{ $key->consumer_id ?? __('Non ancora disponibile') }}" 
                        readonly
                        >
                        <label for="name">{{ __('Consumer ID') }}</label>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <input 
                        type="text" 
                        class="form-control-plaintext" 
                        id="id" 
                        value="{{ $client['client_id'] ?? __('Non ancora disponibile') }}" 
                        readonly
                        >
                        <label for="id">{{ __('client_id') }}</label>
                    </div>
                    <div class="form-group col-md-6">
                        <input 
                        type="text" 
                        class="form-control-plaintext" 
                        id="secret" 
                        value="{{ $client['client_secret'] ?? __('Non ancora disponibile') }}" 
                        readonly
                        >
                        <label for="secret">{{ __('client_secret') }}</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center text-sm-left">
                @component('layouts.components.link_button', [
                    'icon' => 'it-pencil',
                    'link' => $keyEditUrl,
                    'size' => 'lg',
                ])
                {{ ucfirst(__('modifica')) }}
                @endcomponent
                </div>
            @endcomponent
    </div>
</div>
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
            <h4 class="text-uppercase m-0">{{ __('PERMESSI SUI SITI WEB') }}</h4>
            <div class="mt-5 pt-5">
                @include('partials.datatable')
            </div>
            <div class="mt-4 text-center text-sm-left">
                @component('layouts.components.link_button', [
                    'icon' => 'it-pencil',
                    'link' => $keyEditUrl,
                    'size' => 'lg',
                ])
                {{ ucfirst(__('modifica')) }}
                @endcomponent
                </div>
         @endcomponent
    </div>
</div>
@endsection
