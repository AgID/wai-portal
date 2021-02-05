@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __("Aggiungi un'amministrazione"))
@section('title-description')
    @include('pages.websites.partials.callout_custom')
@endsection

@section('content')
    @include('pages.websites.partials.add_primary', ['hideTitle' => true, 'customForm' => $customForm ?? false ])
@endsection
