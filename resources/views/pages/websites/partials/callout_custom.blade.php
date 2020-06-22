@if (!app()->environment('production') && config('wai.custom_public_administrations', false) && !Auth::user()->isA(UserRole::SUPER_ADMIN))
    <p>Continua con una pubblica amministrazione esistente oppure creane una di prova.</p>
@endif
