@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __("Le tue amministrazioni"))

@section('content')
    <div class="mb-3">
        @include('partials.datatable')
        @include('pages.pa.partials.buttons')
    </div>
@endsection
