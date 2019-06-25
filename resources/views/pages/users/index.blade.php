@extends('layouts.default')

@section('title', __('ui.pages.users.index.title'))

@section('content')
    @include('partials.datatable')
    @if (auth()->user()->can(UserPermission::MANAGE_USERS) && auth()->user()->cannot(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.link_button', [
            'label' => __('ui.pages.users.index.add_user'),
            'href' => route('users.create', [], false)
        ])
    @endif
@endsection
