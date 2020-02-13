Hello {{ $publicAdministration->rtd_name ??? '' }}

as the *Digital Transition Manager* we inform you that
your public administration **{{ $publicAdministration->name }}** was
successfully registered on [{{ config('app.name') }}]({{ url('/') }}) by
*{{ $registeringUser->full_name }}* which, on site activation, will become
administrator user of your PA on {{ config('app.name_short') }}}.
