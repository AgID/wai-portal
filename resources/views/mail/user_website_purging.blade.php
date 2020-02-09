@component('mail::message')
# {{ __('Sito web in eliminazione') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_purging_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_purging_message'],
    ['user' => $user, 'website' => $website]
)

@endcomponent
