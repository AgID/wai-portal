@extends('layouts.page', ['graphicBackground' => true])

@section('title', "Aggiungi Chiave")

@section('content')
@include('pages.credentials.partials.form', ['route' => $credentialsStoreUrl])
@endsection