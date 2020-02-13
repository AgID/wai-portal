@component('mail::message')
# {{ __('Nuovo indirizzo mail RTD') }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.rtd_email_changed_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.rtd_email_changed_message'],
    ['publicAdministration' => $publicAdministration, 'earliestRegisteredAdministrator' => $earliestRegisteredAdministrator]
)

@endcomponent
