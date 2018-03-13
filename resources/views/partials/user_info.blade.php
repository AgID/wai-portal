<div class="u-padding-bottom-m">
    <div class="u-inlineBlock">
        {{ __('auth.status.info') }}
        <a href="#tooltip_status" class="Tooltip-toggle u-textClean u-padding-right-xs u-padding-left-xs
            u-background-teal-50 u-color-black" data-menu-trigger="tooltip_status">
            {{ __('auth.status.'.auth()->user()->status) }}
        </a>
        <span id="tooltip_status" data-menu class="Dropdown-menu u-borderShadow-m u-background-teal-70 u-color-white u-layout-prose
            u-padding-r-all u-borderRadius-l">
            <span class="Icon-drop-down Dropdown-arrow u-color-teal-70"></span>
            <span class="u-layout-prose u-text-r-xs">
                {{ __('auth.status.'.auth()->user()->status.'_description') }}
            </span>
        </span>
    </div>
    <div class="u-inlineBlock">
        {{ __('auth.roles.info') }}
        <a href="#tooltip_role" class="Tooltip-toggle u-textClean u-padding-right-xs u-padding-left-xs
            u-background-teal-50 u-color-black" data-menu-trigger="tooltip_role">
            {{ __('auth.roles.'.auth()->user()->roles()->first()->name) }}
        </a>
        <span id="tooltip_role" data-menu class="Dropdown-menu u-borderShadow-m u-background-teal-70 u-color-white u-layout-prose
            u-padding-r-all u-borderRadius-l">
            <span class="Icon-drop-down Dropdown-arrow u-color-teal-70"></span>
            <span class="u-layout-prose u-text-r-xs">
                {{ __('auth.roles.'.auth()->user()->roles()->first()->name.'_description') }}
            </span>
        </span>
    </div>
</div>