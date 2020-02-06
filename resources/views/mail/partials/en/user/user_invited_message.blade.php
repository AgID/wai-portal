Hello {{ $user->name }},

we sent an invitation to **{{ $invitedUser->full_name }}** for your
public administration **{{ $publicAdministration->name }}** on
[{{ config('app.name') }}]({{ url('/') }}).

You will receive a notification when the user
will have completed the profile activation procedure.
