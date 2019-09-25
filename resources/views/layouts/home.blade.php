@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'navbar' => true,
        'classes' => ['home'],
    ])

    <div id="main">
        @yield('content')
    </div>
@endsection
