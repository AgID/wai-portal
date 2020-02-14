Hello {{ $publicAdministration->rtd_name ?? '' }}

as *Digital Transition Manager* we inform you that
your Public Administration **{{ $publicAdministration->name }}** is
registered on [{{ config('app.name') }}]({{ url('/') }}).

@if($publicAdministration->status->is(PublicAdministrationStatus::PENDING))
Your administration is currently awaiting activation.
@endif

For more information you can contact
{{ $earliestRegisteredAdministrator->full_name }} that {{ $publicAdministration->status->is(PublicAdministrationStatus::PENDING) ? 'registered' : 'manages' }}
your PA on {{ config('app.name_short') }}.

**You are receiving this email because your contact details have just been entered on the IndexPA.**
