@component('mail::message')
# {{ __('Utente sospeso') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_suspended_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_suspended_message'],
    ['user' => $user, 'suspendedUser' => $suspendedUser]
)

@endcomponent
