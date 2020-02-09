@component('mail::message')
# {{ __('Utilizzo di un invito scaduto') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_expired_invitation_link_visited_message', 'mail.partials.' . config('app.fallback_locale') . '.user.user_expired_invitation_link_visited_message'],
    ['user' => $user, 'invitedUser' => $invitedUser]
)

@endcomponent
