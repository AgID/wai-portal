@extends('layouts.default')

@section('title', __('ui.pages.dashboard.title'))

@section('content')
    @include('partials.user_info')
    @include('partials.link_button', [
        'label' => __('ui.pages.dashboard.websites'),
        'href' => route('websites-index')
    ])
    @include('partials.link_button', [
        'label' => __('ui.pages.dashboard.users'),
        'href' => route('users-index')
    ])
@endsection
