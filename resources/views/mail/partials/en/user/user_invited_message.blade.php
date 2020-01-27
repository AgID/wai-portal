Hi {{ $user->name }}

an invite to [{{ config('app.name') }}]({{ url('/') }}) was
successfully sent to the user {{ $invitedUser->full_name }}  for your
public administration ({{ $publicAdministration->name }}).

You will receive a notification when he/she
will have completed the activation procedure.
