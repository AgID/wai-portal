<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ __(':app è il portale delle statistiche dei siti web della Pubblica Amministrazione italiana.', ['app' => config('app.name')]) }}">

    <title>{{ config('app.name') }} - @yield('title')</title>

    <link media="all" rel="stylesheet" href="{{ mix('/css/app.css') }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('/icons/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('/icons/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('/icons/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('/icons/safari-pinned-tab.svg') }}" color="#0066cc">
    <link rel="shortcut icon" href="{{ asset('/icons/favicon.ico') }}">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="msapplication-config" content="{{ asset('/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="#ffffff">

    @stack('styles')

    @include('layouts.includes.tracking')
</head>
