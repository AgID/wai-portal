@component('mail::message')
# {{ __('Pubblica amministrazione eliminata') }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.purged_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.purged_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
