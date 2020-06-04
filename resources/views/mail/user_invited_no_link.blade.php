@component('mail::message')
# {{ __('Nuovo utente invitato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_invited_no_link_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_invited_no_link_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
