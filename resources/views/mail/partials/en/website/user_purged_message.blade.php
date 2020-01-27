Hi {{ $user->name }},

the website "{{ $website->name }}" was
removed because we did not received any data within {{ config('wai.purge_expiry') }} days
from the registration on [{{ config('app.name') }}]({{ url('/') }}).
