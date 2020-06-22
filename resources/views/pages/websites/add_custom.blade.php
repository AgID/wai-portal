@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __('Siti web'))
@section('title-description')
    @include('pages.websites.partials.callout_custom')
@endsection

@section('content')
    @include('pages.websites.partials.add_primary', ['customForm' => true])
    @include('pages.pa.partials.buttons')
@endsection
