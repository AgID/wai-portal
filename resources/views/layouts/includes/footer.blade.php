<footer class="Footer">
    <div class="Grid Grid--withGutter">
        <div class="Footer-logo-container--owner Grid-cell u-md-size1of2 u-lg-size5of12 u-padding-r-bottom">
            <a href="{{ $site['owner_link'] }}" class="u-linkClean u-text-sm-s u-text-md-m u-text-lg-m u-inlineBlock u-color-white">
                <img class="Footer-logo u-padding-all-none" src="{{ asset($site['owner_logo']) }}" alt="{{ __('ui.owner_full') }}">
                <span class="Footer-logo-separator"></span>
                <span class="Owner-name">{{ __('ui.owner_full') }}</span>
            </a>
        </div>

        <div class="Grid-cell u-md-size1of2 u-lg-size1of3 u-padding-r-bottom">
            <p class="u-padding-bottom-l" style="opacity: 0.5">{{ __('ui.in_collaboration_with') }}</p>
            <a href="{{ $site['partner_link'] }}" class="u-linkClean u-text-r-m u-inlineBlock u-color-white">
                <img class="Footer-logo u-padding-all-none u-margin-right-s" src="{{ asset($site['gov_logo']) }}" alt="{{ __('ui.partner_full') }}">
                <img class="Footer-logo u-padding-all-none" src="{{ asset($site['partner_logo']) }}" alt="{{ __('ui.partner_full') }}">
                <span class="Footer-logo-separator--partner"></span>
                <span class="Partner-name">{{ __('ui.partner_full') }}</span>
            </a>
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
