@component('mail::message')
# {{ __('Sito web aggiunto') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_added_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_added_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
