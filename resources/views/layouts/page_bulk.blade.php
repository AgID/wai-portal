@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'navbar' => false,
        'classes' => ['tall'],
    ])

    <div class="position-relative">
        <div class="page-bulk-container {{ ($wideLayout ?? false) ? 'wide-layout' : '' }}">
            <div id="main" role="main">
                @yield('before-title')

                <h1 class="display-2">@yield('title')@yield('title-after')</h1>

                @yield('content')
            </div>
        </div>
        <div class="page-background-image">
            <img alt="" src="{{ asset('images/page-bulk-bg.svg') }}" alt="">
        </div>
    </div>
@endsection
