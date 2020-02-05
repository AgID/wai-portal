@component('mail::message')
# {{ __('Nuovo sito web aggiunto') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_added_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_added_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
