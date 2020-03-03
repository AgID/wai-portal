@extends('layouts.page')

@section('title', $user->name)

@section('title-after')
<small class="text-muted ml-3">{{ __('modifica') }}</small>
@endsection

@section('content')
@include('pages.admin.users.partials.form', ['route' => route('admin.users.update', ['user' => $user])])
@endsection
