Hello {{ $user->full_name }},

thank you for registering on [{{ config('app.name') }}]({{ url('/') }})!

In order to complete the registration
you need to confirm your email address.

@component('mail::button', ['link' => $signedUrl])
    Confirm email
@endcomponent

If you are unable to click the button above,
you can copy and paste the following link in the address bar of the
browser: `{!! $signedUrl !!}`

**You can confirm your email address
within {{ config('auth.verification.expire', 7) }} days since receiving this
message.**
