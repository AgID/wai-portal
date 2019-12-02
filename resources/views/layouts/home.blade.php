@extends('layouts.default')

@section('page-content')
    @include('layouts.includes.header', [
        'navbar' => true,
        'classes' => ['home'],
    ])

    <div id="main" role="main">
        @yield('content')
    </div>
@endsection
