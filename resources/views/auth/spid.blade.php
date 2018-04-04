@php
    session()->now('message', ['warning' => "La risorsa richiesta richiede l'accesso."]);
@endphp
{{-- //TODO: put message in lang file --}}
@extends('layouts.page')

@section('title', __('ui.pages.spid-auth_login.title'))

@section('page-content')
    @can('access-admin-area')
        @include('spid-auth::spid-button', ['size' => 'l'])
    @else
        <div class="agid-spid-enter-button" aria-live="polite" data-size="l"></div>
    @endcan
@endsection
