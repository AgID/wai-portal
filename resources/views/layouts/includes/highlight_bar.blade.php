@if ($hasHighlightBar)
<div id="highlightBar" class="highlight-bar analogue-2-bg-a3 d-flex justify-content-center py-3 sticky-top">
    <div class="neutral-1-color mx-4 text-center">
        @markdown(config('site.highlight')[config('app.env')])
        @if($hasResetCountdown)
        <span class="font-weight-semibold py-1 px-2 mt-1 rounded d-inline-block analogue-1-bg text-white">
            {{ __("Prossimo reset tra: :days.", ['days' => $countdown]) }}
        </span>
        @endif
    </div>
</div>
@endif
