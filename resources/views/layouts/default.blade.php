<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">

    @include('layouts.includes.head')

    <body>

        @include('layouts.includes.cookiebar')

        @yield('page-content')

        @include('layouts.includes.scrolltop')
        @include('layouts.includes.footer')
        @include('layouts.includes.modal')
        @include('layouts.includes.notification')
        @include('layouts.includes.scripts')
        @stack('scripts')

    </body>
</html>
