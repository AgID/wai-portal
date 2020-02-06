Hello {{ $user->name }},

the new user **{{ $activatedUser->full_name }}** has been successfully
activated for your public administration.

You can now manage him/her using the
[user management page]({{ url(route('users.index')) }}).
