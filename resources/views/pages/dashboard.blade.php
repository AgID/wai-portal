@extends('layouts.default')

@section('title', __('ui.pages.dashboard.title'))

@section('content')
    @can(UserPermission::ACCESS_ADMIN_AREA)
        @include('partials.link_button', [
            'label' => __('ui.pages.dashboard.users'),
            'href' => route('admin.publicAdministration.users.index', ['publicAdministration' => $publicAdministration], false)
        ])
        @include('partials.link_button', [
            'label' => __('ui.pages.dashboard.websites'),
            'href' => route('admin.publicAdministration.websites.index', ['publicAdministration' => $publicAdministration], false)
        ])
    @else
        @include('partials.user_info')
        @include('partials.link_button', [
            'label' => __('ui.pages.dashboard.websites'),
            'href' => route('websites.index', [], false)
        ])
        @include('partials.link_button', [
            'label' => __('ui.pages.dashboard.users'),
            'href' => route('users.index', [], false)
        ])
    @endcan
@endsection
