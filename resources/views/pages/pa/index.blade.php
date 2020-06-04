@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __("Le tue amministrazioni"))

@section('content')
    <div class="mb-3">
        @if($hasPublicAdministrations)
            @include('partials.datatable')
        @else
            <div class="callout mw-100 text-center">
                <p class="mw-100">Al momento non appartieni a nessuna pubblica addministrazione.</p>
            </div>
        @endif
        @include('pages.pa.partials.buttons')
    </div>
@endsection
