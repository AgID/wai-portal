@extends('layouts.page_bulk', ['graphicBackground' => true])

@section('title', __("Le tue amministrazioni"))

@section('content')
    <div class="mb-5">
        @include('partials.datatable')
        @include('pages.pa.partials.buttons')
    </div>
@endsection
