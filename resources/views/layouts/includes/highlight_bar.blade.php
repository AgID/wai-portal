@if ($hasHighlightBar)
<div id="highlightBar" class="highlight-bar analogue-2-bg-a3 d-flex justify-content-center py-3 sticky-top">
    <div class="neutral-1-color mx-4 text-center">
        @markdown(config('site.highlight')[config('app.env')])
        @if($hasResetCountdown)
        {{ __("Reset dell'ambiente in :days", ['days' => $countdown]) }}
        @endif
    </div>
</div>
@endif
