@extends('layouts.default')

@section('title', __('ui.pages.404.title'))

@section('content')
  <div class="u-flex u-flexCol u-flexJustifyCenter u-flexAlignItemsCenter">
    <p class="u-color-50 u-textWeight-600" style="font-size: 6em">404</p>
    <p>{!! __('ui.pages.404.not_found', ['page' => '<code>'.$not_found_path.'</code>']) !!}</p>
    <p><a href="{{ route('home', [], false) }}">{{ __('ui.return_home') }}</a></p>
  </div>
@endsection
