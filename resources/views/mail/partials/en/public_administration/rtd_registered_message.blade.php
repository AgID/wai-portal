Hello {{ $publicAdministration->rtd_name ??? '' }}

as the *Digital Transition Manager* we inform you that
your public administration **{{ $publicAdministration->name }}** was
successfully registered on [{{ config('app.name') }}]({{ url('/') }}).
