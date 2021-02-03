@extends('layouts.page', ['graphicBackground' => true])

@section('title', "Aggiungi Credenziale")

@section('content')
@include('pages.credentials.partials.form', ['route' => $credentialsStoreUrl])
@endsection