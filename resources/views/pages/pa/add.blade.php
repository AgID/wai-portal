@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __("Aggiungi un'amministrazione'"))

@section('content')
    @include('pages.websites.partials.add_primary', ['hideTitle' => true])
    @include('pages.pa.partials.buttons')
@endsection
