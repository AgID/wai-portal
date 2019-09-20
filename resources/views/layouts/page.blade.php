@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header')

    @section('page-container')
        @if ($graphicBackground ?? false)
        <div class="page-background-graphic">
        @include('layouts.includes.graphic_background')
        @endif

        <div class="container mt-2 mt-sm-3 mt-lg-5">
        @if (Breadcrumbs::exists())
        @include('layouts.includes.breadcrumbs', ['breadcrumbs' => Breadcrumbs::generate()])
        @endif

        @include('layouts.includes.alert')
        </div>

        <div id="main">
            <div class="container">
            <h1>@yield('title')@yield('title-after')</h1>
            </div>

            @unless ($fullWidth ?? false)
            <div class="container mb-2 mb-sm-3 mb-lg-5">
            @endunless

            @yield('content')
            
            @unless ($fullWidth ?? false)
            </div>
            @endunless
            <div class="absolute-bottom page-background-image">
                <img alt="" src="{{ asset('images/page-bg.svg') }}">
            </div>
        </div>
        @if ($graphicBackground ?? false)
        </div>
        @endif
    @show
@endsection
