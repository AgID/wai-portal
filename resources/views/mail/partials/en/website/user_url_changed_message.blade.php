Hi {{ $user->name }},

the URL of the website "{{ $website->name }}" registered
on [{{ config('app.name') }}]({{ url('/') }}) for
your public administration ({{ $publicAdministration }}),
has been modified.

You can view the changes by accessing the [section for website management]({{ url(route('websites.index')) }}).

