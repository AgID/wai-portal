@include('layouts.includes.head')
@include('layouts.includes.skiplinks')
@include('layouts.includes.cookiebar')

<div class="u-background-95">
    <div class="u-layout-wide u-layoutCenter">
        @include('layouts.includes.header')
        @include('layouts.includes.offcanvas')
        @include('layouts.includes.network')
        <div class="Prose u-background-grey-15 u-layout-r-withGutter u-padding-r-top u-padding-bottom-xxl u-posRelative">
            @include('layouts.includes.breadcrumbs', ['breadcrumbs' => Breadcrumbs::generate()])
            <div id="main" style="min-height: 50vh">{{-- //TODO: remove inline style --}}
                @include('layouts.includes.alert')
                <h1 class="u-text-h1">@yield('title')</h1>
                @yield('content')
            </div>
        </div>
    </div>
</div>

@include('layouts.includes.scrolltop')

<div class="u-background-95">
  <div class="u-layout-wide u-layoutCenter u-layout-r-withGutter u-hiddenPrint">
    @include('layouts.includes.footer')
  </div>
</div>

@include('layouts.includes.scripts')
@stack('scripts')

</body>
</html>
