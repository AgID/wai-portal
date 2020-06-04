@component('mail::message')
# {{ __('Utente sospeso') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.suspended_message', 'mail.partials.' . config('app.fallback_locale') . '.user.suspended_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
