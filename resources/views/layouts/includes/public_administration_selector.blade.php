<div class="form-group m-0">
    <form method="post" action="{{ $selectTenantUrl }}" novalidate class="pa-selector" >
        @csrf
        <div class="input-group flex-nowrap">
            <div class="input-group-prepend">
                <div class="input-group-text border-0 rounded-left"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use></svg></div>
            </div>
            <div class="bootstrap-select-wrapper rounded-right w-100">
                <select title="{{ __("Scegli un'amministrazione") }}" id="public-administration" name="public-administration" data-live-search="true" data-live-search-placeholder="Cerca...">
                    @foreach($publicAdministrationSelectorArray as $publicAdministrationsSelectorOption)
                        @if ($isSuperAdmin)
                        <option value="{{ $publicAdministrationsSelectorOption['ipa_code'] }}" {{ ($publicAdministrationsSelectorOption['ipa_code'] === session('super_admin_tenant_ipa_code')) ? "selected" : "" }}>
                        @else
                        <option value="{{ $publicAdministrationsSelectorOption['ipa_code'] }}" {{ ($publicAdministrationsSelectorOption['id'] === session('tenant_id')) ? "selected" : "" }}>
                        @endif
                            {{ $publicAdministrationsSelectorOption['name'] }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="target-route" value="{{ $targetRoute }}" >
                <input type="hidden" name="target-route-pa-param" value="{{ $targetRouteHasPublicAdministrationParam }}" >
            </div>
        </div>
    </form>
</div>
