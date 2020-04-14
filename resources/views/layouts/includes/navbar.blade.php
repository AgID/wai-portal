<div class="it-header-navbar-wrapper{{ $isSuperAdmin ? ' super-admin' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="navbar navbar-expand-lg" aria-label="{{ __('navigazione principale') }}">
                    <button class="custom-navbar-toggler" type="button" aria-controls="main-nav" aria-expanded="false" aria-label="{{ __('apri il menu di navigazione') }}" data-target="#main-nav">
                        <svg class="icon">
                            <use xlink:href="{{ asset('svg/sprite.svg#it-burger') }}"></use>
                        </svg>
                    </button>
                    <div class="navbar-collapsable" id="main-nav">
                        <div class="overlay"></div>
                        <div class="close-div sr-only">
                            <button class="btn close-menu" type="button"><span class="it-close"></span>{{ __('chiudi') }}</button>
                        </div>
                        <div class="menu-wrapper">
                            @if ($authUser || $spidAuthUser)
                            <ul class="navbar-nav primary">
                                @foreach ($primaryMenuArray as $primaryMenuItem)
                                <li class="nav-item">
                                    <a class="nav-link{{ $primaryMenuItem['active'] ? ' active' : '' }}{{ $primaryMenuItem['disabled'] ? ' disabled' : '' }}"
                                        {{ $primaryMenuItem['disabled'] ? 'aria-disabled="true"' : '' }}
                                        href="{{ $primaryMenuItem['url'] }}">
                                        <span>{{ __($primaryMenuItem['name']) }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="col-lg-4 ml-auto">
                                <div class="input-group flex-nowrap">
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
                            </div>
                            @endif
                            @unless($isSuperAdmin)
                            <ul class="navbar-nav secondary ml-auto">
                                @foreach (config('site.menu_items.secondary') as $secondaryMenuItem)
                                <li class="nav-item">
                                    <a class="nav-link{{ request()->route()->named($secondaryMenuItem['route']) ? ' active' : '' }}" href="{{ route($secondaryMenuItem['route']) }}">
                                        <span>{{ __($secondaryMenuItem['name']) }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            @endunless
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</div>
