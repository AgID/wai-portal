@component('mail::message')
# {{ __('Sito web attivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_activated_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_activated_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
