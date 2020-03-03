@component('mail::message')
# {{ __('Pubblica amministrazione registrata su :app', ['app' => config('app.name')]) }}

@includeFirst(
    ['mail.partials.' . $locale . '.public_administration.rtd_registered_message', 'mail.partials.' . config('app.fallback_locale') . '.public_administration.rtd_registered_message'],
    [
        'publicAdministration' => $publicAdministration,
        'registeringUser' => $registeringUser,
    ]
)

@endcomponent
