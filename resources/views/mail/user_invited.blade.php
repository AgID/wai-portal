@component('mail::message')
# {{ __('Nuovo utente invitato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_invited_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_invited_message'],
    ['user' => $user, 'invitedUser' => $invitedUser, 'publicAdministration' => $publicAdministration]
)

@endcomponent
