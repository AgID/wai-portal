@component('mail::message')
# {{ __('mail.website.purging.user.title') }}

@include('mail.partials.' .$locale . '.user.website_purging_message', ['fullName' => $fullName, 'website' => $website])

@endcomponent
