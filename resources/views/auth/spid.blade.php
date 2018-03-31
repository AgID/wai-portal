@php
    session()->now('message', ['warning' => 'La risorsa richiesta richiede l\'accesso.']);
@endphp
{{-- //TODO: put message in lang file --}}
@extends('layouts.page')

@section('title', __('ui.pages.spid-auth_login.title'))

@section('page-content')
    @include('spid-auth::spid-button', ['size' => 'l'])
@endsection
