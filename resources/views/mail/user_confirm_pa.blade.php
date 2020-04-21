@component('mail::message')
# {{ __('Nuovo utente invitato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_confirm_pa_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_confirm_pa_message'],
    ['user' => $user, 'verificationUrl' => $verificationUrl, 'publicAdministration' => $publicAdministration]
)

@endcomponent
