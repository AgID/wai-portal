Hi {{ $user->name }},

the website "{{ $website->name }}" has been
successfully activated; from now on, you can manage it
by accessing the portal [{{ config('app.name') }}]({{ url('/') }}).
