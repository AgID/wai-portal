@component('mail::message')
# {{ __('Sito web riattivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_unarchived_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_unarchived_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
