@component('mail::message')
    # {{ __('mail.website.archived.user.title') }}

    @include('mail.partials.' .$locale . '.user.website_archived_message', ['fullName' => $fullName, 'website' => $website, 'expire' => $expire])

@endcomponent
