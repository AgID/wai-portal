@extends('layouts.default')

@section('title', __('ui.pages.admin.users.index.title'))

@section('content')
    @include('partials.datatable')
    @include('partials.link_button', [
        'label' => __('ui.pages.admin.users.index.add_user'),
        'href' => route('admin.users.create', [], false)
    ])
@endsection
