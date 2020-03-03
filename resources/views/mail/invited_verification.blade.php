@component('mail::message')
# {{ __('Invito su :app', ['app' => config('app.name')]) }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.invited_verification_message', 'mail.partials.' . config('app.fallback_locale') . '.user.invited_verification_message'],
    ['publicAdministration' => $publicAdministration, 'signedUrl' => $signedUrl]
)

@endcomponent
