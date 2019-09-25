@extends('layouts.page')

@section('title', __('Visualizzazione log'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @include('pages.logs.partials.filters')
    @include('partials.datatable')
    @endcomponent
@endsection
