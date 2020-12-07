@extends('layouts.page', ['graphicBackground' => true])

@section('title', "API swagger")

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        <div class="mt-4 text-center text-sm-right">
            @component('layouts.components.link_button', [
                'link' => $keys,
                'size' => 'lg',
            ])
            {{ ucfirst(__('Gestione delle credenziali OAuth')) }}
            @endcomponent
        </div>
        <div id="swagger-ui" />
        @endcomponent
    </div>
</div>
@endsection