@extends('layouts.page')

@section('title', __('Aggiungi un sito web'))

@section('content')
@include('pages.websites.partials.form', ['route' => $websiteStoreUrl])
@endsection
