Hello,

you have been invited for your public administration
**{{ $publicAdministration->name }}** on [{{ config('app.name') }}]({{ url('/') }}).

In order to confirm you must click the link below.

@component('mail::button', ['link' => $verificationUrl])
    Accept invite
@endcomponent

If you are unable to click the button above,
copy and paste the following link into the address bar of
the browser: `{!! $verificationUrl !!}`

**You can confirm your email address
within {{ config('auth.verification.expire', 7) }} days since receiving this
messagge.**
