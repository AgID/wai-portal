@extends('layouts.page')

@section('title', __('Invita un utente'))

@section('content')
@include('pages.users.partials.form', [
    'route' => $userStoreUrl,
    'emailFieldName' => 'email',
    'emailFieldValue' => null,
])
@endsection
