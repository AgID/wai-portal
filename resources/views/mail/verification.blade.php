@component('mail::message')
# {{ __('Account creato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.verification_message', 'mail.partials.' . config('app.fallback_locale') . '.user.verification_message'],
    ['user' => $user, 'signedUrl' => $signedUrl]
)

@endcomponent
