@component('mail::message')
# {{ __('Utente riattivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_reactivated_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_reactivated_message'],
    ['user' => $user, 'reactivatedUser' => $reactivatedUser]
)

@endcomponent
