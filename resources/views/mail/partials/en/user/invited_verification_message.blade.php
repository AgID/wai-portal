Hi,

you have been invited for your PA ({{ $publicAdministration->name }})
on [{{ config('app.name') }}]({{ url('/') }}).

In order to complete your registration, you must login with your SPID
account and confirm your email.

@component('mail::button', ['link' => $signedUrl])
    Login and confirm email
@endcomponent

If you are unable to click the button above,
copy and paste the following link into the address bar of
the browser: `{!! $signedUrl !!}`

**You can confirm your email address
within {{ config('auth.verification.expire', 7) }} days since receiving this
messagge.**
