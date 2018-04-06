@if (auth()->check() && auth()->user()->status == 'active' && auth()->user()->publicAdministration)
    <div class="u-layout-r-withGutter">
        <div class="Label--publicAdministration">
            {{ str_limit(auth()->user()->publicAdministration->name, 40) }}
        </div>
    </div>
@endif
