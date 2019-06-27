@extends('layouts.default')

@section('title', __('ui.pages.users.index.title'))

@section('content')
    @include('partials.datatable')
    @if (auth()->user()->can(UserPermission::MANAGE_USERS) || auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.link_button', [
            'label' => __('ui.pages.users.index.add_user'),
            'href' => auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? route('admin.publicAdministration.users.create', ['publicAdministration' => request()->route('publicAdministration')]) : route('users.create', [], false)
        ])
    @endif
@endsection
