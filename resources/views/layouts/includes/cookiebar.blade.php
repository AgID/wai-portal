<div id="cookie-bar" class="CookieBar js-CookieBar u-background-95 u-padding-r-all" aria-hidden="true">
  <p class="u-color-white u-text-r-xs u-lineHeight-m u-padding-r-bottom">
    {{ __('ui.cookiebar_msg') }}<br>
  </p>
  <p>
    <button class="Button Button--default u-text-r-xxs js-cookieBarAccept u-inlineBlock u-margin-r-all">{{ __('ui.cookiebar_accept') }}</button>
    <a href="{{ route('privacy', [], false) }}" class="u-text-r-xs u-color-teal-50">{{ __('ui.cookiebar_privacy_policy') }}</a>
  </p>
</div>
