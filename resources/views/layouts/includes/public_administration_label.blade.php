@if (auth()->check() && auth()->user()->status == 'active' && auth()->user()->publicAdministration)
    <div class="u-layout-r-withGutter u-cf u-sizeFull u-textCenter u-background-80">
        <div class="Label--publicAdministration u-text-r-xxs u-color-white u-padding-top-xs u-padding-bottom-xs">
            {{ auth()->user()->publicAdministration->name }}
        </div>
    </div>
@endif
