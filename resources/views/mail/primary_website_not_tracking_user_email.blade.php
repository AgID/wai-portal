@component('mail::message')
# {{ __('mail.website.primary_not_tracking.user.title') }}

@include('mail.partials.' . $locale . '.user.primary_website_not_tracking_message', ['fullName' => $fullName])

@endcomponent
