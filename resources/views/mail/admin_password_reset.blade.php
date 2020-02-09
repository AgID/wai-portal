@component('mail::message')
# {{ __('Reset della password') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.admin_password_reset_message', 'mail.partials.' . config('app.fallback_locale') . '.user.admin_password_reset_message'],
    ['user' => $user, 'token' => $token]
)

@endcomponent
