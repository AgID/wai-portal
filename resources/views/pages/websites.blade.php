@extends('layouts.default')

@section('title', __('ui.pages.websites.title'))

@section('content')
    @include('partials.datatable')
    @can('manage-sites')
    @include('partials.link_button', [
        'label' => __('ui.pages.websites.add_site'),
        'href' => route('websites-create')
    ])
    @endcan
@endsection
