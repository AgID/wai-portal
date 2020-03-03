@component('mail::message')
# {{ __('Tracciamento sito istituzionale non attivo') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_primary_not_tracking_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_primary_not_tracking_message'],
    ['user' => $user]
)

@endcomponent
