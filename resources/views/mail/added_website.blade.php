@component('mail::message')
# {{ __('Nuovo sito web aggiunto') }}

@includeFirst(
    ['mail.partials.' . $locale . '.website.added_message', 'mail.partials.' . config('app.fallback_locale') . '.website.added_message'],
    ['user' => $user, 'website' => $website, 'javascriptSnippet' => $javascriptSnippet]
)

@endcomponent
