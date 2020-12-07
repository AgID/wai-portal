@extends('layouts.page', ['graphicBackground' => true])

@section('title', "Aggiungi Chiave")

@section('content')
@include('pages.keys.partials.form', ['route' => $keysStoreUrl])
@endsection