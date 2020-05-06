@component('mail::message')
# {{ __('Utente riattivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.reactivated_message', 'mail.partials.' . config('app.fallback_locale') . '.user.reactivated_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
