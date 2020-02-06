@if (config('site.highlight')[config('app.env')] ?? false)
<div class="mail-highlight-bar">
    <div class="mail-highlight-bar-content">
        @markdown(config('site.highlight')[config('app.env')])
    </div>
</div>
@endif
