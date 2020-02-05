Hello {{ $user->name }},

the website "{{ $website->name }}" has been
added on [{{ config('app.name') }}]({{ url('/') }}).
