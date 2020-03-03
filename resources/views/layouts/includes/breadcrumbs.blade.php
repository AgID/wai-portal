@if (count($breadcrumbs) > 1)
    <nav class="breadcrumb-container" aria-label="{{ __('sei qui:') }}">
        <ol class="breadcrumb p-0">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="breadcrumb-item">
                    <a href="{{ $loop->last ? '#main' : $breadcrumb->url }}" aria-label="{{ $breadcrumb->title }} - {{ __('vai al contenuto') }}">{{ $breadcrumb->title }}</a>
                    @if (!$loop->last)
                    <span class="separator">&gt;</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
