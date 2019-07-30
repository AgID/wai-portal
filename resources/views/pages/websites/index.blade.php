@extends('layouts.default')

@section('title', __('ui.pages.websites.index.title'))

@section('content')
    @include('partials.datatable')
    @if (auth()->user()->can(UserPermission::MANAGE_WEBSITES) && auth()->user()->cannot(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.link_button', [
            'label' => __('ui.pages.websites.index.add_website'),
            'href' => route('websites.create', [], false)
        ])
    @endif
@endsection
