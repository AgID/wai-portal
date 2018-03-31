@extends('layouts.default')

@section('title', __('ui.pages.429.title'))

@section('content')
    <div class="u-flex u-flexCol u-flexAlignItemsCenter u-margin-top-xxl">
        <p class="u-textWeight-600 u-textCenter u-text-xxl">{!! nl2br(__('ui.pages.429.description')) !!}</p>
        <p><a href="{{ route('home', [], false) }}">{{ __('ui.return_home') }}</a></p>
    </div>
@endsection
