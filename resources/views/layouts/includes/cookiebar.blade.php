<div class="cookiebar">
    <p>
        {{ __('Questo sito utilizza cookie tecnici, analytics e di terze parti.') }}
        <br>
        {{ __("L'uso dei cookie richiede il tuo consenso.") }}</p>
    <div class="cookiebar-buttons">
    <a href="{{ config('site.privacy_policy') }}" class="cookiebar-btn">{{ __('Privacy policy') }}</a>
        <button data-accept="cookiebar" class="cookiebar-btn cookiebar-confirm">{{ __('Accetto') }}<span class="sr-only">{{ __(' i cookie') }}</span></button>
    </div>
</div>
