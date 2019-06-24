@extends('layouts.default')

@section('title', __('ui.pages.users.index.title'))

@section('content')
    @include('partials.datatable')
    @can(UserPermission::MANAGE_USERS)
        @include('partials.link_button', [
            'label' => __('ui.pages.users.index.add_user'),
            'href' => route('users.create', [], false)
        ])
    @endcan
@endsection
