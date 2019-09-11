<a href="{{ $link }}" role="button" class="btn btn-{{ $type ?? 'primary' }}{{ empty($icon) ? '' : ' btn-icon' }}{{ isset($size) ? " btn-{$size}" : ''}}">
    <span>{{ $label }}</span>
    @isset($icon)
    <svg class="icon icon-white ml-1">
        <use xlink:href="{{ asset('svg/sprite.svg') }}#{{ $icon }}"></use>
    </svg>
    @endisset
</a>
