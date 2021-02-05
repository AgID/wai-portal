@extends('layouts.page')

@section('title', __('Dashboard amministrazioni'))

@section('content')
    @if($hasPublicAdministrations)
    @include('partials.datatable')
    @else
    <div class="callout mw-100 text-center">
        <p class="mw-100">{{ 'Al momento non ci sono amministrazioni registrate.' }}</p>
    </div>
    @endif
@endsection
