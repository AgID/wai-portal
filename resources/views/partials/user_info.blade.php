<div class="u-padding-bottom-m">
    <p class="u-text-p">
        {{ __('auth.status.info') }}
        <a href="#tooltip_status" class="Tooltip-toggle u-textClean u-padding-right-xs u-padding-left-xs u-color-black" data-menu-trigger="tooltip_status">
            {{ auth()->user()->status->description }}
        </a>
        <span id="tooltip_status" data-menu class="Tooltip Dropdown-menu u-borderShadow-m u-background-teal-70 u-color-white u-layout-prose u-padding-r-all u-borderRadius-l">
            <span class="Icon-drop-down Dropdown-arrow u-color-teal-70"></span>
            <span class="u-layout-prose u-text-r-xs">
                {{ auth()->user()->status->description }}
            </span>
        </span>
    </p>
    @if (auth()->user()->isAn(UserRole::ADMIN))
    <p class="u-text-p">
        Sei amministratore della tua PA.
    </p>
    @endif
</div>
