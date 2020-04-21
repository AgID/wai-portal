<div class="form-group">
    <form method="post" action="{{ $postRoute }}" novalidate class="pa-selector" >
        @csrf
        <div class="input-group flex-nowrap">
            <div class="input-group-prepend">
                <div class="input-group-text border-0 rounded-left"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use></svg></div>
            </div>
            <div class="bootstrap-select-wrapper rounded-right w-100">
                <select title="Scegli un'amministrazione" id="public-administration-nav" name="public-administration-nav" data-live-search="true" data-live-search-placeholder="Cerca...">
                    @foreach($publicAdministrationSelectorArray as $publicAdministrationsSelectorOption)
                        @if ($isSuperAdmin)
                        <option value="{{ $publicAdministrationsSelectorOption['ipa_code'] }}" {{ ($publicAdministrationsSelectorOption['ipa_code'] === session('super_admin_tenant_ipa_code')) ? "selected" : "" }}>
                            {{ $publicAdministrationsSelectorOption['name'] }}
                        </option>
                        @else
                        <option value="{{ $publicAdministrationsSelectorOption['id'] }}" {{ ($publicAdministrationsSelectorOption['id'] == session('tenant_id')) ? "selected" : "" }}>
                            {{ $publicAdministrationsSelectorOption['name'] }}
                        </option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="return-route" value="{{ $returnRoute }}" >
                <input type="hidden" name="has-route-param-pa" value="{{ $hasRouteParampublicAdministration }}" >
            </div>
        </div>
    </form>
</div>
