@component('mail::message')
# {{ __('Password modificata') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.admin_password_changed_message', 'mail.partials.' . config('app.fallback_locale') . '.user.admin_password_changed_message'],
    ['user' => $user]
)

@endcomponent
