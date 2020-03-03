@extends('layouts.page')

@section('title', $website->name)

@section('title-after')
<small class="text-muted ml-3">{{ __('modifica') }}</small>
@endsection

@section('content')
@include('pages.websites.partials.form', ['route' => $updateUrl])
@endsection
