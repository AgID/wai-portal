@component('mail::message')
# {{ __('URL sito web modificato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_url_changed_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_url_changed_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
