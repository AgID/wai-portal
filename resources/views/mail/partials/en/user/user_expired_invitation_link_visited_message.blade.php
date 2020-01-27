Hi {{ $user->name }},

the user {{ $invitedUser->full_name }} has tried to activate his/her account
to the portal [{{ config('app.name') }}]({{ url('/') }}), but his/her
link is expired.

If you wish, you can send him/her a new invitation link from his [profile]({{ url(route('users.show', ['user' => $this->invitedUser])) }}).
