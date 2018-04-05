@extends('layouts.default')

@section('title', __('ui.pages.403.title'))

@section('content')
    <div class="u-layoutCenter u-layout-prose u-flex u-flexCol u-flexAlignItemsCenter u-margin-top-xxl">
        <p class="u-textWeight-600 u-textCenter u-text-xxl">{{ $exception->getMessage() }}</p>
        <p><a href="{{ route('home', [], false) }}">{{ __('ui.return_home') }}</a></p>
    </div>
@endsection
