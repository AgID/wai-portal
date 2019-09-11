@extends('layouts.page')

@section('title', __('Aggiungi un utente super amministratore'))

@section('content')
@include('pages.admin.users.partials.form', ['route' => route('admin.users.store')])
@endsection
