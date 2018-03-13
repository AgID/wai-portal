<!DOCTYPE html>
<!--[if IE 8]><html lang="it" class="no-js ie89 ie8"><![endif]-->
<!--[if IE 9]><html lang="it" class="no-js ie89 ie9"><![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="it" class="no-js" prefix="og: http://ogp.me/ns#">
<!--<![endif]-->

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ __('ui.site_title') }} - @yield('title')</title>

  <link rel="preload" href="{{ asset('/toolkit/IWT.min.js') }}" as="script">

  <script type="text/javascript">
    WebFontConfig = {
      google: {
        families: ['Titillium+Web:300,400,600,700,400italic:latin']
      }
    };
    (function() {
      var wf = document.createElement('script');
      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      wf.type = 'text/javascript';
      wf.async = 'true';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(wf, s);
    })();
  </script>

  <!-- include html5shim per Explorer 8 -->
  <script src="{{ asset('/toolkit/vendor/modernizr.js') }}"></script>

  <link media="all" rel="stylesheet" href="{{ asset('/toolkit/build.css') }}">
  <link media="all" rel="stylesheet" href="{{ asset('/css/app.css') }}">

  <script src="{{ asset('/toolkit/vendor/jquery.min.js') }}"></script>

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
<body class="{{ $site['css_theme'] }}">
