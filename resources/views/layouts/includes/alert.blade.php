@if (session()->has('message'))
    @foreach (session('message') as $type => $text)
        <div class="Alert Alert--{{ $type }} Alert--withIcon u-padding-r-right u-margin-r-bottom" role="alert">
            <p class="u-text-p">{{ $text }}</p>
        </div>
    @endforeach
@endif

@if (session()->has('messages'))
    @foreach (session('messages') as $message)
        @foreach ($message as $type => $text)
            <div class="Alert Alert--{{ $type }} Alert--withIcon u-padding-r-right u-margin-r-bottom" role="alert">
                <p class="u-text-p">{{ $text }}</p>
            </div>
        @endforeach
    @endforeach
@endif
