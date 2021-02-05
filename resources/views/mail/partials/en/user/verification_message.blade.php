Hello {{ $user->full_name }},

you have entered a new e-mail address on [{{ config('app.name') }}]({{ url('/') }})!

Verification is required to complete the procedure.

@component('mail::button', ['link' => $signedUrl])
    Verify email
@endcomponent

If you cannot complete the verification by clicking on the button,
you can copy and paste the following link in the address bar of the
browser: `{!! $signedUrl !!}`

**You can verify your email address
within {{ config('auth.verification.expire', 7) }} days since receiving this
message.**

*If you think you have received this message by mistake, please write to
{{ config('site.owner.mail') }}.*
