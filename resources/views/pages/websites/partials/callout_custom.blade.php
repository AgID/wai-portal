@unlessenv ('production')
    @if (config('wai.custom_public_administrations', false) && !$isSuperAdmin && !session()->has('tenant_id'))
        <p>{{ __('Continua con una pubblica amministrazione esistente oppure creane una di prova.') }}</p>
    @endif
@endenv
