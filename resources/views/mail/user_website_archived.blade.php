@component('mail::message')
# {{ __('Sito web archiviato') }}

@includeFirst(
    ['mail.partials.' .$locale . '.website.user_archived_message','mail.partials.' . config('app.fallback_locale') . '.website.user_archived_message'],
    ['user' => $user, 'website' => $website, 'manually' => $manually]
)

@endcomponent
