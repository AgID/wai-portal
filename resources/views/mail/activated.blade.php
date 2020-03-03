@component('mail::message')
# {{ __('Utente attivato') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.activated_message', 'mail.partials.' . config('app.fallback_locale') . '.user.activated_message'],
    ['user' => $user]
)

@endcomponent
