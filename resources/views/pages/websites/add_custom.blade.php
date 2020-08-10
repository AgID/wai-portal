@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Siti web'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @include('pages.websites.partials.add_primary', ['customForm' => true])
    @endcomponent
@endsection
