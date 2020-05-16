@component('mail::message')
# {{ __('Nuovo utente invitato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.invited_message', 'mail.partials.' . config('app.fallback_locale') . '.user.invited_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
