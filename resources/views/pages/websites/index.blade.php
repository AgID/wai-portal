@extends('layouts.default')

@section('title', __('ui.pages.websites.index.title'))

@section('content')
    @include('partials.datatable')
    @can('manage-sites')
        @include('partials.link_button', [
            'label' => __('ui.pages.websites.index.add_website'),
            'href' => route('websites-add', [], false)
        ])
    @endcan
@endsection
