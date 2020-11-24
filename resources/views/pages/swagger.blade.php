@extends('layouts.page', ['graphicBackground' => true])

@section('title', "API swagger")

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        <div id="swagger-ui" />
        @endcomponent
    </div>
</div>
@endsection