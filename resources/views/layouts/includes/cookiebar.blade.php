<div class="cookiebar" role="contentinfo" aria-label="{{ __('gestione delle preferenze sui cookie') }}">
    <p>
        {{ __('Questo sito utilizza cookie tecnici, analytics e di terze parti.') }}
    <div class="cookiebar-buttons">
    <a href="{{ config('site.privacy_policy') }}" class="cookiebar-btn">{{ __('Privacy policy') }}</a>
        <button data-accept="cookiebar" class="cookiebar-btn cookiebar-confirm">{{ __('Accetto') }}
            <span class="sr-only">{{ __('i cookie') }}</span>
        </button>
    </div>
</div>
