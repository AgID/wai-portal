Hi {{ $user->name }},

the new user {{ $activatedUser->full_name }} has been successfully
activated for your public administration.

You can now manage him/her by logging into the portal [{{ config('app.name') }}]({{ url('/') }})
and using the [section for user management]({{ url(route('users.index')) }}).
