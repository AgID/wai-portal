@extends('layouts.default')

@section('title', __('ui.pages.admin.users.edit.title'))

@section('content')
    @include('partials.admin_user_form', ['route' => route('admin.users.update', ['user' => $user], false), 'method' => 'PATCH'])
@endsection
