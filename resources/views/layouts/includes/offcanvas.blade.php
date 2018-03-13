<section class="Offcanvas Offcanvas--right Offcanvas--modal js-fr-offcanvas u-jsVisibilityHidden u-nojsDisplayNone u-hiddenPrint is-ready" id="menu">
	<h2 class="u-hiddenVisually">{{ __('ui.navigation_menu_label') }}</h2>
  <div class="Offcanvas-content u-background-white">
    <div class="Offcanvas-toggleContainer u-background-50 u-jsHidden">
      <a class="Hamburger-toggleContainer u-block u-color-white u-padding-bottom-xxl u-padding-left-s u-padding-top-xxl js-fr-offcanvas-close"
        aria-controls="menu" aria-label="{{ __('ui.navigation_menu_esc') }}" title="{{ __('ui.navigation_menu_esc') }}" href="#">
        <span class="Hamburger-toggle is-active" aria-hidden="true"></span>
      </a>
    </div>
    <nav>
      @include('layouts.includes.linklist')
    </nav>
  </div>
</section>
