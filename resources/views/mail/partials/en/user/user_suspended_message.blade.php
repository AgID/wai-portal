Hello {{ $user->name }},

the user **{{ $suspendedUser->full_name }}** has been suspended.

You can reactivate it at any time from the
[user management page]({{ url(route('users.index')) }}).
