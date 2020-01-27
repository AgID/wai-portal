@component('mail::message')
# {{ __('Pubblica Amministrazione registrata') }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.registered_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.registered_message'],
    ['user' => $user, 'publicAdministration' => $publicAdministration, 'javascriptSnippet' => $javascriptSnippet]
)

@endcomponent
