@component('mail::message')
# {{ __('Sito web eliminato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_purged_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_purged_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
