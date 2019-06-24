@extends('layouts.default')

@section('title', __('ui.pages.admin.users.add.title'))

@section('content')
    @include('partials.admin_user_form', ['route' => route('admin.users.store', [], false)])
@endsection
