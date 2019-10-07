@component('mail::message')
# {{ __('mail.website.activated.user.title') }}

@include('mail.partials.' .$locale . '.user.website_activated_message', ['fullName' => $fullName, 'website' => $website])

@endcomponent
