@component('mail::message')
# {{ __('Modifica URL sito web') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_url_changed_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_url_changed_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
