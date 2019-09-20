@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'navbar' => true,
        'classes' => ['home'],
    ])

    @include('layouts.includes.alert')

    <div id="main">
        @yield('content')
    </div>
@endsection
