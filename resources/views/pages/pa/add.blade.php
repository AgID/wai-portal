@extends('layouts.page_bulk', ['graphicBackground' => true])

@section('title', __("Aggiungi un sito istituzionale"))

@section('content')
    @include('pages.websites.partials.add_primary', ['hideTitle' => true])
    @include('pages.pa.partials.buttons')
@endsection
