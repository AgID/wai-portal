@extends('layouts.page_bulk', ['graphicBackground' => true])

@section('title', __('Seleziona pubblica amministrazione'))

@section('content')
    <div class="mb-5 pa-select-full">
        <p>{{ __("Seleziona l'amministrazione che vuoi gestire.") }}</p>
        @include('layouts.includes.public_administration_selector')
        @include('pages.pa.partials.buttons')
    </div>
@endsection
