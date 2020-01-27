@component('mail::message')
# {{ __('Pubblica amministrazione attiva') }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.activated_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.activated_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration]
)

@endcomponent
