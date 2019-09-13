@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'authUser' => auth()->user(),
        'spidAuthUser' => app()->make('SPIDAuth')->getSPIDUser(),
        'hasActivePublicAdministration' => session()->has('tenant_id') && auth()->user()->status->is(UserStatus::ACTIVE),
    ])

    @empty($fullWidth)<div class="container my-5">@endempty
        @section('page-inner-container')
        @if (Breadcrumbs::exists())
        @include('layouts.includes.breadcrumbs', ['breadcrumbs' => Breadcrumbs::generate()])
        @endif

        <div id="main">

            @include('layouts.includes.alert')

            <h1>@yield('title')@yield('title-after')</h1>

            @yield('content')

        </div>
        @show
    @empty($fullWidth)</div>@endempty
@endsection
