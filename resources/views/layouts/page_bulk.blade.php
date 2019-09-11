@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'navbar' => false,
        'tallHeader' => true,
        'authUser' => auth()->user(),
        'hasActivePublicAdministration' => session()->has('tenant_id') && auth()->user()->status->is(UserStatus::ACTIVE),
    ])

    <div class="page-container">
        <div id="main">

            @include('layouts.includes.alert')
            @yield('before-title')

            <h1 class="display-2">@yield('title')@yield('title-after')</h1>

            @yield('content')

        </div>
    </div>
@endsection
