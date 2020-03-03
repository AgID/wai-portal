Hello {{ $user->name }},

the public administration **{{ $publicAdministration->name }}**
has been correctly activated on [{{ config('app.name') }}]({{ url('/') }}).

From this moment on, the traffic data related to the institutional site
of the administration will be collected and processed by the platform.

If you need support on this or other arguments related to {{ config('app.name_short') }},
you can browse the [user guide]({{ config('site.kb.link') }}).
