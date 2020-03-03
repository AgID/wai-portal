@component('mail::message')
# {{ __('Invito su :app', ['app' => config('app.name')]) }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.admin_invited_verification_message', 'mail.partials.' . config('app.fallback_locale') . '.user.admin_invited_verification_message'],
    ['user' => $user, 'signedUrl' => $signedUrl]
)

@endcomponent
