<header class="Header u-hiddenPrint">
    <div class="Header-banner u-background-60">
        <div class="Header-owner Headroom-hideme u-flex u-flexJustifyBetween u-flexAlignItemsCenter">
            <div class="Header-institutional">
                <div class="u-md-hidden u-lg-hidden u-border-right-xxs u-margin-right-xs u-padding-right-xs u-inlineBlock u-alignMiddle">
                    <a class="js-fr-offcanvas-open u-block" href="#network" aria-controls="network" aria-label="{{ __('ui.toggle_menu_label') }}" title="{{ __('ui.toggle_menu_label') }}">
                        <img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB3aWR0aD0iMTkuMnB4IiBoZWlnaHQ9IjE2cHgiIHZpZXdCb3g9IjAgMCAxMiAxMCIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KICAgIDxkZWZzPjwvZGVmcz4KICAgIDxnIGlkPSIxMDI0dXAiIHN0cm9rZT0ibm9uZSIgc3Ryb2tlLXdpZHRoPSIxIiBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxnIGlkPSItLWhvbWUtLS1wYXJhbGxheC0tLW1vYmlsZSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTI0LjAwMDAwMCwgLTIwLjAwMDAwMCkiIGZpbGw9IiNGRkZGRkYiPgogICAgICAgICAgICA8ZyBpZD0iLW5ldHdvcmstc2xpbS1oZWFkZXIiPgogICAgICAgICAgICAgICAgPHBhdGggZD0iTTI0LDI0IEwzNiwyNCBMMzYsMjYgTDI0LDI2IEwyNCwyNCBaIE0yNCwyMCBMMzIsMjAgTDMyLDIyIEwyNCwyMiBMMjQsMjAgWiBNMjQsMjggTDMyLDI4IEwzMiwzMCBMMjQsMzAgTDI0LDI4IFoiIGlkPSJpY29uLXNtYWxsLWJ1cmdlciI+PC9wYXRoPgogICAgICAgICAgICA8L2c+CiAgICAgICAgPC9nPgogICAgPC9nPgo8L3N2Zz4=" alt="{{ __('ui.toggle_menu_label') }}"/>
                    </a>
                </div>
                <a href="{{ $site['owner_link'] }}" class="u-color-white u-text-xxs u-linkClean u-inlineBlock u-textWeight-400">
                    <span class="u-inline u-md-hidden u-lg-hidden u-sm-hidden">
                        {{ __('ui.owner_short') }}
                    </span>
                    <span class="u-hidden u-md-inline u-lg-inline u-sm-inline">
                        {{ __('ui.owner_short') }}
                    </span>
                </a>
                <span class="u-color-white">+</span>
                <a href="{{ $site['partner_link'] }}" class="u-color-white u-text-xxs u-linkClean u-inlineBlock u-textWeight-400">
                    <span class="u-inline u-md-hidden u-lg-hidden u-sm-hidden">
                        {{ __('ui.partner_short') }}
                    </span>
                    <span class="u-hidden u-md-inline u-lg-inline u-sm-inline">
                        {{ __('ui.partner_full') }}
                    </span>
                </a>
            </div>
            <div class="u-hidden u-md-block u-lg-block">
                @include('layouts.includes.slim_header')
            </div>
        </div>
    </div>

    <div class="Header-navbar u-background-50 u-text-r-xxl">
        <div class="u-layout-wide Grid Grid--alignMiddle u-layoutCenter u-flexNoWrap">
            <div class="Header-logo Grid-cell" aria-hidden="true">
                <a href="{{ url('/') }}" tabindex="-1" class="u-linkClean">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iNzguMTg3IiB3aWR0aD0iOTcuMzg3IiB2aWV3Qm94PSIwIDAgOTEuMzAwMDAyIDczLjMwMDAwMiI+PGcgZmlsbD0iI2ZmZiI+PHBhdGggZD0iTTMxLjc5OSA3MS45VjE1LjdoMTUuMVY3MmgtMTUuMXpNOTEuMDk5IDI4LjVoLTEzLjh2MjMuMWMwIDIuMy4xIDMuOC4yIDQuOC4xLjkuNSAxLjcgMS4yIDIuNHMxLjggMSAzLjMgMWw4LjYtLjIuNyAxMmMtNSAxLjEtOC45IDEuNy0xMS41IDEuNy02LjggMC0xMS40LTEuNS0xMy44LTQuNi0yLjUtMy0zLjctOC42LTMuNy0xNi44VjBoMTUuMXYxNS42aDEzLjh2MTIuOXpNOS4wOTkgMzIuOGMtMi42IDAtNC44LS45LTYuNS0yLjdzLTIuNi00LTIuNi02LjYuOS00LjggMi41LTYuNmMxLjctMS44IDMuOS0yLjYgNi41LTIuNnM0LjguOSA2LjUgMi43IDIuNSA0IDIuNSA2LjctLjggNC44LTIuNSA2LjZjLTEuNiAxLjYtMy43IDIuNS02LjQgMi41eiIvPjwvZz48L3N2Zz4=" alt="{{ __('ui.site_title') }}">
                </a>
            </div>

            <div class="Header-title Grid-cell">
                <h1 class="Header-titleLink">
                    <a href="{{ url('/') }}" class="u-linkClean">
                        {{ __('ui.site_title') }}
                        @isset($site['pill'])
                            <span class="Header-titlePill">{{ $site['pill'] }}</span>
                        @endisset
                        <br>
                        <small>{{ __('ui.site_subtitle') }}</small>
                    </a>
                </h1>
            </div>

            <div class="Header-auth Grid-cell u-text-xs u-lg-flexOrderLast u-md-flexOrderLast u-flexExpandLeft u-md-flexExpandRight">
                @if (app()->make('SPIDAuth')->isAuthenticated())
                    <button class="auth-user" aria-controls="auth-user-options" aria-haspopup="true" role="button">
                        <span aria-hidden="true" class="auth-icon">
                            <img class="u-hidden u-md-inline u-lg-inline" aria-hidden="true" src="/vendor/spid-auth/img/spid-ico-circle-bb.svg" onerror="this.src='/vendor/spid-auth/img/spid-ico-circle-bb.png'; this.onerror=null;" alt="Accesso SPID">
                            <span class="u-md-hidden u-lg-hidden auth-icon-text">{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->familyName, 0, 1) }}</span>
                        </span>
                        <span class="auth-text">{{ app()->make('SPIDAuth')->getSPIDUser()->name }} {{ app()->make('SPIDAuth')->getSPIDUser()->familyName }}</span>
                    </button>
                    <div id="auth-user-options" class="u-borderShadow-m u-background-white auth-user-options" role="menu" aria-hidden="true">
                        <span class="Icon-drop-down Dropdown-arrow u-color-white"></span>
                        <ul class="Linklist">
                            <li><a href="{{ route('user-profile', [], false) }}" class="u-color-50 u-padding-r-all u-block u-linkClean">Profilo</a></li>
                            <li><a href="{{ route('spid-auth_logout', [], false) }}" class="u-color-50 u-padding-r-all u-block u-linkClean">Disconnetti</a></li>
                        </ul>
                    </div>
                @else
                    @can('access-admin-area')
                    <button class="auth-user" aria-controls="auth-user-options" aria-haspopup="true" role="button">
                        <span aria-hidden="true" class="auth-icon">
                            <span class="auth-icon-text">{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->familyName, 0, 1) }}</span>
                        </span>
                        <span class="auth-text">{{ auth()->user()->name }} {{ auth()->user()->familyName }}</span>
                    </button>
                    <div id="auth-user-options" class="u-borderShadow-m u-background-white auth-user-options" role="menu" aria-hidden="true">
                        <span class="Icon-drop-down Dropdown-arrow u-color-white"></span>
                        <ul class="Linklist">
                            <li><a href="{{ route('user-profile', [], false) }}" class="u-color-50 u-padding-r-all u-block u-linkClean">Profilo</a></li>
                            <li><a href="{{ route('admin-password_change', [], false) }}" class="u-color-50 u-padding-r-all u-block u-linkClean">Cambio password</a></li>
                            <li><a href="{{ route('admin-logout', [], false) }}" class="u-color-50 u-padding-r-all u-block u-linkClean">Disconnetti</a></li>
                        </ul>
                    </div>
                    @else
                        @include('spid-auth::spid-button', ['size' => 'm'])
                    @endcan
                @endif
            </div>

            <div class="Header-toggle Grid-cell u-flex u-flexAlignItemsCenter u-flexJustifyCenter u-lg-flexGrow1">
                <a class="Hamburger-toggleContainer js-fr-offcanvas-open u-nojsDisplayInlineBlock u-lg-hidden u-md-hidden" href="#menu" aria-controls="menu" aria-label="{{ __('ui.toggle_menu_label') }}" title="{{ __('ui.toggle_menu_label') }}">
                    <span class="Hamburger-toggle" role="presentation"></span>
                    <span class="Header-toggleText" role="presentation">Menu</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Header-navbar -->
    <div class="Headroom-hideme u-textCenter u-hidden u-sm-hidden u-md-block u-lg-block">
        <nav class="Megamenu Megamenu--default js-megamenu u-background-50 u-color-white" data-rel=".Offcanvas.Offcanvas--right .Treeview"></nav>
    </div>

</header>

@push('scripts')
    <script type="text/javascript">
        $('document').ready(function () {
            var dropdown = $('#auth-user-options');

            $('.auth-user').click(function (e) {
                if (dropdown.is(':visible')) {
                    dropdown.hide();
                    dropdown.attr("aria-hidden", "true");
                } else {
                    dropdown.show();
                    dropdown.attr("aria-hidden", "false");
                }
                e.stopPropagation();
            });

            $(document).click(function () {
                dropdown.hide();
                dropdown.attr("aria-hidden", "true");
            });
        });
    </script>
@endpush
