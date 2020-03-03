@component('mail::message')
# {{ __('Sito web attivato su :app', ['app' => config('app.name')]) }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.rtd_activated_message', 'mail.partials.' . config('app.fallback_locale') . '.website.rtd_activated_message'],
    ['publicAdministration' => $publicAdministration, 'website' => $website]
)

@endcomponent
