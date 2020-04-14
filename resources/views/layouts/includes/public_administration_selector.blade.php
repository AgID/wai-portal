<div class="input-group flex-nowrap selector-pa">
    <div class="input-group-prepend">
        <div class="input-group-text border-0 rounded-left"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use></svg></div>
    </div>
    <div class="bootstrap-select-wrapper rounded-right w-100">
        <select title="Scegli un'amministrazione" id="public-administration-nav" name="public-administration-nav" data-live-search="true" data-live-search-placeholder="Cerca...">
            @foreach($publicAdministrationSelectorArray as $publicAdministrationsSelectorOption)
                @if ($isSuperAdmin)
                <option value="{{ $publicAdministrationsSelectorOption['url'] }}" {{ ($publicAdministrationsSelectorOption['ipa_code'] === session('super_admin_tenant_ipa_code')) ? "selected" : "" }}>
                    {{ $publicAdministrationsSelectorOption['name'] }}
                </option>
                @else
                <option value="{{ $publicAdministrationsSelectorOption['url'] }}" {{ ($publicAdministrationsSelectorOption['id'] == session('tenant_id')) ? "selected" : "" }}>
                    {{ $publicAdministrationsSelectorOption['name'] }}
                </option>
                @endif
            @endforeach
        </select>
    </div>
</div>
