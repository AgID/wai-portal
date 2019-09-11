<!-- Header -->
<div class="it-header-wrapper">
    @include('layouts.includes.slim_header')
    <div class="it-nav-wrapper{{ $tallHeader ?? false ? ' tall' : ''}}">
        <div class="it-header-center-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="it-header-center-content-wrapper">
                            <div class="it-brand-wrapper{{ $navbar ? '' : ' p-0' }}">
                                <a href="{{ url('/') }}">
                                    <img alt="{{ config('app.name') }} - logo" class="icon" src="{{ asset(config('site.logo')) }}">
                                    <div class="it-brand-text">
                                        <h2 class="d-flex align-items-center">{{ config('app.name') }} <small><span class="badge badge-light badge-pill ml-2">{{ config('site.pill') }}</span></small></h2>
                                        <h3>{{ __('I dati dei siti web della PA') }}</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="it-right-zone col-auto col-md-6">
                                <div class="it-tenant d-none ml-auto{{ $hasActivePublicAdministration ? ' d-md-block' : '' }}">
                                    {{ optional(current_public_administration())->name }}
                                </div>
                                <div class="it-socials d-none ml-auto{{ $hasActivePublicAdministration ? '' : ' d-md-flex' }}">
                                    <span>{{ __('Seguici su') }}</span>
                                    <ul>
                                        @foreach (config('site.social') as $socialLink)
                                        <li class="ml-3">
                                            <a href="{{ $socialLink['url'] }}" aria-label="{{ $socialLink['name'] }}">
                                                <svg class="icon">
                                                    <use xlink:href="{{ asset('svg/sprite.svg') }}#it-{{ $socialLink['name'] }}"></use>
                                                </svg>
                                            </a>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @includeWhen($navbar, 'layouts.includes.navbar', [
            $isSuperAdmin = isset($authUser) && $authUser->isA(UserRole::SUPER_ADMIN),
        ])
    </div>
</div>
<!-- End Header -->
