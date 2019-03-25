@extends('layouts.default')

@section('title', __('ui.pages.403.title'))

@section('content')
    <div class="u-layoutCenter u-layout-prose u-flex u-flexCol u-flexAlignItemsCenter u-margin-top-xxl">
        <p class="u-textWeight-600 u-textCenter u-text-xxl">{!! nl2br(__('ui.pages.403.description')) !!}</p>
        <p class="u-color-90 u-text-xxl u-margin-top-xl">{{ $exception->getMessage() }}</p> {{-- TODO: remove in production --}}
        <p><a href="{{ route('home', [], false) }}">{{ __('ui.return_home') }}</a></p>
    </div>
@endsection
