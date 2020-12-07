@extends('layouts.page')

@section('title', $key->client_name)

@section('title-after')
<small class="text-muted ml-3">{{ __('modifica') }}</small>
@endsection

@section('content')
@include('pages.keys.partials.form', ['route' => $updateUrl])
@endsection
