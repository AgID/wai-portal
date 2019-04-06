@if (auth()->check() && auth()->user()->status->is(\App\Enums\UserStatus::ACTIVE) && session()->has('tenant_id'))
    <div class="u-layout-r-withGutter u-cf u-sizeFull u-textCenter u-background-80">
        <div class="Label--publicAdministration u-text-r-xxs u-color-white u-padding-top-xs u-padding-bottom-xs">
            {{ current_public_administration()->name }}
        </div>
    </div>
@endif
