@component('mail::message')
# {{ __('Utente cancellato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.deleted_message', 'mail.partials.' . config('app.fallback_locale') . '.user.deleted_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
