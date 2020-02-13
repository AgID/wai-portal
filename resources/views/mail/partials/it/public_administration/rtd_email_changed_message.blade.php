Ciao {{ $publicAdministration->rtd_name ?? '' }}

in qualità di Responsabile per la transizione al digitale ti informiamo che
la tua Pubblica Amministrazione **{{ $publicAdministration->name }}** è
registrata su [{{ config('app.name') }}]({{ url('/') }}).

@if($publicAdministration->status->is(PublicAdministrationStatus::PENDING))
Al momento la tua amministrazione è in attesa di attivazione.
@endif

Per avere maggiori informazioni puoi rivolgerti a
{{ $earliestRegisteredAdministrator->full_name }} che {{ $publicAdministration->status->is(PublicAdministrationStatus::PENDING) ? 'ha registrato' : 'amministra' }}
la tua PA su {{ config('app.name_short') }}.

**Ricevi questa mail perché il tuo recapito è stato da poco inserito su IndicePA.**
