@component('mail::message')
# {{ __('Verifica indirizzo email') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.verification_message', 'mail.partials.' . config('app.fallback_locale') . '.user.verification_message'],
    ['user' => $user, 'signedUrl' => $signedUrl]
)

@endcomponent
