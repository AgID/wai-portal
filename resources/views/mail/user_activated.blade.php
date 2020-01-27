@component('mail::message')
# {{ __('Utente attivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_activated_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_activated_message'],
    ['user' => $user, 'activatedUser' => $activatedUser]
)

@endcomponent
