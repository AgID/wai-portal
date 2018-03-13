@if (count($breadcrumbs) > 1)
<nav aria-label="sei qui:" role="navigation"> {{-- TODO: implement breadcrumbs --}}
    <ul class="Breadcrumb">
        @foreach ($breadcrumbs as $breadcrumb)
            <li class="Breadcrumb-item">
                <a class="Breadcrumb-link u-color-50" href="{{ $loop->last ? '#main' : $breadcrumb->url }}">{{ $breadcrumb->title }}</a>
            </li>
        @endforeach
    </ul>
</nav>
@endif
