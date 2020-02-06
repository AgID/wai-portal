Hello {{ $user->name }},

please note that the user permissions **{{ $modifiedUser->full_name }}** have been
modified.

You can check the new permissions from his/her
[profile]({{ url(route('users.show', ['user' => $this->invitedUser]) }}).
