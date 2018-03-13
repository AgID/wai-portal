<footer class="Footer">
  <div class="Grid Grid--withGutter">
    <div class="Footer-logo-container--owner Grid-cell u-md-size1of2 u-lg-size5of12 u-padding-r-bottom">
      <a href="{{ $site['owner_link'] }}" class="u-linkClean u-text-r-m u-inlineBlock u-color-white" title="{{ __('ui.owner_full') }}">
        <img class="Footer-logo u-padding-all-none" src="{{ asset($site['owner_logo']) }}" alt="{{ __('ui.owner_full') }}">
        <span class="Footer-logo-separator"></span>
        <span class="Owner-name">{{ __('ui.owner_full') }}</span>
      </a>
    </div>

    <div class="Grid-cell u-md-size1of2 u-lg-size1of3 u-padding-r-bottom">
      <p class="u-padding-bottom-l" style="opacity: 0.5">{{ __('ui.in_collaboration_with') }}</p>
      <a href="{{ $site['partner_link'] }}" class="u-linkClean u-text-r-m u-inlineBlock u-color-white" title="{{ __('ui.partner_full') }}">
        <img class="Footer-logo u-padding-all-none u-margin-right-s" src="{{ asset($site['gov_logo']) }}" alt="{{ __('ui.partner_full') }}">
        <img class="Footer-logo u-padding-all-none" src="{{ asset($site['partner_logo']) }}" alt="{{ __('ui.partner_full') }}">
        <span class="Footer-logo-separator--partner"></span>
        <span class="Partner-name">{{ __('ui.partner_full') }}</span>
      </a>
      {{--<p class="u-padding-bottom-l" style="opacity: 0.5">{{ __('ui.in_collaboration_with') }}</p>
      <div class="Grid">
        <div class="u-inlineBlock">
          <a href="{{ $site['partner_link'] }}" class="u-linkClean">
            <img class="u-padding-top-xxs u-padding-right-xs" src="{{ asset($site['gov_logo']) }}" alt="" style="height: 4em; max-width: 100%">
            <img class="u-padding-top-xxs u-padding-right-xxs" src="{{ asset($site['partner_logo']) }}" alt="" style="height: 4em; max-width: 100%">
          </a>
        </div>
        <div class="Grid-cell u-size1of12 u-padding-left-none team-bar"></div>
        <p class="Grid-cell u-size5of12 Footer-siteName u-padding-left-xs u-text-r-xs">
          <a href="{{ $site['partner_link'] }}" class="u-color-white u-linkClean">
            {{ strtoupper(__('ui.partner_full')) }}
          </a>
        </p>
      </div>--}}
    </div>

    <div class="Social Grid-cell u-lg-sizeFull u-flex u-flexJustifyEnd u-textRight u-padding-r-bottom u-flexAlignSelfEnd">
      <h2 class="u-md-flexInline u-lg-flexInline u-text-p u-color-white u-textWeight-400 u-hidden u-margin-r-right u-flexAlignSelfCenter">{{ ucfirst(__('ui.follow_us')) }}</h2>
      <ul class="Footer-socialIcons">
        @foreach ($site['social'] as $social)
        <li>
          <a href="{{ $social['link'] }}" title="{{ $social['name'] }}">
            <span class="Icon-{{ $social['name'] }}"></span>
            <span class="u-hiddenVisually">{{ ucfirst($social['name']) }}</span>
          </a>
        </li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="Footer-links u-cf u-color-80">
    <ul class="u-floatLeft">
      @foreach ($site['footer_links'] as $link)
      <li><a href="{{ url($link['url']) }}">{{ ucfirst(__('ui.'.$link['title'])) }}</a></li>
      @endforeach
    </ul>
    <ul class="Footer-notes u-floatRight">
      <li class="u-color-grey-50">{{ __('ui.version') }}: {{ config('app.version') }}</li>
    </ul>
  </div>
</footer>
