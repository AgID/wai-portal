Hello {{ $user->name }},

we inform you that the public administration **{{ $publicAdministration->name }}**
has been removed from [{{ config('app.name') }}]({{ url('/') }}) because we
didn't receive any data within {{ config('wai.purge_expiry') }} days
from the registration.

From now on it will no longer be possible to use the tracking code
for the institutional website.

If you still want to track the web traffic data of your public
administration with [{{ config('app.name_short') }}]({{ url('/') }}), you will have to
to do a new registration.
