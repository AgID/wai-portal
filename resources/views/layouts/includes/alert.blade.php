@if (session()->has('alert'))
    @foreach (session('alert') as $type => $text)
    <div class="alert alert-{{ $type }} rounded fade show" role="alert">
        {{ $text }}
        <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('chiudi') }}">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endforeach
@endif
