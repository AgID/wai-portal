Hello {{ $publicAdministration->rtd_name ??? '' }}

as the *Digital Transition Manager* we inform you that the website
**{{ $website->name }}** of your public administration
**{{ $publicAdministration->name }}** has been correctly activated on
[{{ config('app.name') }}]({{ url('/') }}).
