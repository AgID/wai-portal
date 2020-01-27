@component('mail::message')
# {{ __('Sito web in archiviazione') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.user_archiving_message', 'mail.partials.' . config('app.fallback_locale') . '.website.user_archiving_message'],
    ['user' => $user, 'website' => $website, 'daysLeft' => $daysLeft,]
)

@endcomponent
