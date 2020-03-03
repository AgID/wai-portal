Hello {{ $user->name }},

the website **{{ $website->name }}** has been
successfully registered on [{{ config('app.name') }}]({{ url('/') }}).

@include('mail.partials.en.website.tracking_instructions', ['javascriptSnippet' => $javascriptSnippet])

For more support on this topic, you can visit the
[{{ config('app.name_short') }} guide](https://docs.italia.it/).
