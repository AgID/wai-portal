Hello {{ $user->name }},

we received a request to reset your password for [{{ config('app.name') }}]({{ url('/') }}).

If you made it because you can't login,
use this button to navigate to the password change page.

@component('mail::button', ['link' => route('admin.password.reset.show', $token)])
    Change password
@endcomponent

If you are unable to click the button above,
you can visit this page [{{ url(route('admin.password.reset.show')) }}]({{ url(route('admin.password.reset.show')) }})
and insert the following code:
`{!! $token !!}`

**You can modify your password within one hour since making the request
(until {{ $user->passwordResetToken->created_at->addHour()->format('H:i:s') }}
{{ $user->passwordResetToken->created_at->addHour()->format('d/m/Y') }}).**

**If you did not requested a password change, ignore this email.**
