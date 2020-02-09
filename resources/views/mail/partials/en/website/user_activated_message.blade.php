Hello {{ $user->name }},

the website **{{ $website->name }}** has been
successfully activated.

You can now manage it using the
[websites management page]({{ url(route('websites.index')) }}).
