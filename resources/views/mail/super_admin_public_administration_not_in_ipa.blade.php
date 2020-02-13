@component('mail::message')
# {{ __('Pubblica Amministrazione non trovata in iPA') }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.not_found_in_ipa_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.not_found_in_ipa_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
