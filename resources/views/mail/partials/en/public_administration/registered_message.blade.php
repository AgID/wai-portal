Hello {{ $user->name }},

the public administration **{{ $publicAdministration->name }}** has been
correctly registered to [{{ config('app.name') }}]({{ url('/') }}).

@include('mail.partials.it.website.tracking_instructions', ['javascriptSnippet' => $javascriptSnippet])

For more support on this topic, you can browse
the [{{ config('app.name_short') }} guide]({{ config('site.kb.link') }}).
