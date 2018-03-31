<section class="Network Offcanvas Offcanvas--left Offcanvas--modal js-fr-offcanvas fr-offcanvas--left u-jsVisibilityHidden u-nojsDisplayNone u-hiddenPrint is-ready" id="network">
    <h2 class="u-hiddenVisually">{{ __('ui.navigation_network_menu_label') }}</h2>
    <div class="Offcanvas-content u-background-white">
        <div class="Offcanvas-toggleContainer u-background-50 u-jsHidden">
            <a class="Hamburger-toggleContainer u-block u-color-white u-padding-bottom-xxl u-padding-left-s u-padding-top-xxl js-fr-offcanvas-close" aria-controls="network" aria-label="{{ __('ui.navigation_menu_esc') }}" title="{{ __('ui.navigation_menu_esc') }}" href="#">
                <span class="Hamburger-toggle is-active" aria-hidden="true"></span>
            </a>
        </div>
        <nav>
            <ul class="Linklist Linklist--padded u-layout-prose u-text-r-xs">
                @foreach ($site['network_links'] as $link)
                    <li class="{{ $link['class'] ?? '' }}">
                        {{ $item['li_markup_pre'] ?? '' }}
                        <a href="{{ $link['url'] }}" class="Linklist-link">
                            {{ __('ui.'.$link['title']) }}
                        </a>
                        {{ $item['li_markup_post'] ?? '' }}
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</section>
