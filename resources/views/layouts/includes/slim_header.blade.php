<div class="Header-slim">
    <ul class="Header-links u-cf">
        @foreach ($site['network_links'] as $link)
            <li class="u-color-white {{ $link['class'] ?? '' }}"><a class="u-color-white u-linkClean u-text-xxs u-textWeight-300" href="{{ $link['url'] }}">{{ __('ui.'.$link['title']) }}</a></li>
        @endforeach
    </ul>
</div>
