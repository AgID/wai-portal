@component('mail::message')
# {{ __('mail.website.archiving.user.title') }}

@include('mail.partials.' .$locale . '.user.website_archiving_message', ['fullName' => $fullName, 'website' => $website, 'daysLeft' => $daysLeft,])

@endcomponent
