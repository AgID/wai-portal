Ciao {{ $user->name }},

la pubblica amministrazione **{{ $publicAdministration->name }}** è stata
registrata correttamente su [{{ config('app.name') }}]({{ url('/') }}).

@include('mail.partials.it.website.tracking_instructions', ['javascriptSnippet' => $javascriptSnippet])

Per avere supporto su questo argomento, puoi consultare
la [guida di {{ config('app.name_short') }}]({{ config('site.kb.link') }}).
