@component('mail::message')
# {{ __('Permessi modificati') }}

@includeFirst(
    ['mail.partials.' . $locale . '.user.user_website_access_changed', 'mail.partials.' . config('app.fallback_locale') . '.user.user_website_access_changed'],
    ['user' => $user, 'modifiedUser' => $modifiedUser]
)

@endcomponent
