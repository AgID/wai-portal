@extends('layouts.page', ['fullWidth' => true])

@section('title', __('Visualizzazione log'))

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        @component('layouts.components.box', ['classes' => 'rounded'])
        @include('pages.logs.partials.filters')
        @include('partials.datatable')
        @endcomponent
    </div>
</div>
@endsection
