Hello {{ $user->name }},

you have been invited on [{{ config('app.name') }}]({{ url('/') }}) as
**super administrator**.

In order to complete your registration, you must confirm your
email.

@component('mail::button', ['link' => $signedUrl])
    Confirm email
@endcomponent

If you are unable to click the button above,
copy and paste the following link into the address bar of
the browser: `{!! $signedUrl !!}`

**You can confirm your email address
within {{ config('auth.verification.expire', 7) }} days since receiving this
message.**
