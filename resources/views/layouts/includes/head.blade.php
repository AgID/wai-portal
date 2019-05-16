<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('ui.site_title') }} - @yield('title')</title>

    <link media="all" rel="stylesheet" href="{{ asset('/css/app.css') }}">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('/icons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('/icons/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('/icons/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('/icons/manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('/icons/safari-pinned-tab.svg') }}">
    <link rel="shortcut icon" href="{{ asset('/icons/favicon.ico') }}">
    <meta name="apple-mobile-web-app-title" content="{{ __('ui.site_title') }}">
    <meta name="application-name" content="{{ __('ui.site_title') }}">
    <meta name="msapplication-config" content="{{ asset('/icons/browserconfig.xml') }}">
    <meta name="theme-color" content="{{ $site['theme_color'] }}">

    @stack('styles')

    @include('layouts.includes.tracking')
</head>
