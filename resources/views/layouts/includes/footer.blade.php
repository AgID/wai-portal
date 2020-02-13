<!-- Footer -->
<footer class="it-footer">
    <div class="it-footer-main">
        <div class="container">
            <section aria-label="{{ __('informazioni nel footer') }}" role="contentinfo">
                <div class="row clearfix align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="it-brand-wrapper">
                            <a class="d-inline-flex" href="{{ config('site.owner.link') }}">
                                <img alt="{{ config('site.owner.name') }} - logo" src="{{ asset(config('site.owner.logo')) }}">
                            </a>
                        </div>
                    </div>
                    <div class="pb-5 py-md-0 ml-md-auto">
                        <div class="d-flex">
                            <span class="mr-2">{{ __('Seguici su') }}</span>
                            <ul class="list-inline text-left m-0">
                                @foreach (config('site.social') as $socialLink)
                                    <li class="list-inline-item ml-1">
                                        <a class="text-white" href="{{ $socialLink['url'] }}" aria-label="{{ $socialLink['name'] }}">
                                            <svg class="icon icon-sm icon-white align-top">
                                                <use xlink:href="{{ asset('svg/sprite.svg') }}#it-{{ $socialLink['name'] }}"></use>
                                            </svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="footer-primary-menu row justify-content-between mb-4">
                    @foreach (config('site.footer_links.primary') as $footerPrimaryMenuItem)
                    <div class="p-2">
                        <h4>
                            <a href="{{ isset($footerPrimaryMenuItem['route']) ? route($footerPrimaryMenuItem['route']) : $footerPrimaryMenuItem['url'] }}"
                                {{ isset($footerPrimaryMenuItem['url']) ? 'class="external-link" target="_blank" rel="noopener noreferrer"' : ''}}>
                                {{ __($footerPrimaryMenuItem['name']) }}</a>
                        </h4>
                    </div>
                    @endforeach
                </div>
                @include('layouts.includes.footer_band')
            </section>
        </div>
    </div>
    <div class="it-footer-small-prints clearfix">
        <div class="container d-flex align-items-end align-items-md-center">
            <nav aria-label="{{ __('navigazione secondaria') }}">
                <ul class="it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row pl-0">
                    @foreach (config('site.footer_links.secondary') as $footerSecondaryMenuItem)
                    <li class="list-inline-item px-0">
                        <a class="nav-link" href="{{ route($footerSecondaryMenuItem['route']) }}">
                            <span class="font-weight-semibold analogue-2-color">{{ __($footerSecondaryMenuItem['name']) }}</span>
                        </a>
                    </li>
                    @endforeach
                    @can(UserPermission::ACCESS_ADMIN_AREA)
                    <li class="list-inline-item px-0">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <span class="font-weight-semibold analogue-2-color">{{ __('Dashboard amministrativa') }}</span>
                        </a>
                    </li>
                    <li class="list-inline-item px-0">
                        <a class="nav-link" href="{{ route('admin.logs.show') }}">
                            <span class="font-weight-semibold analogue-2-color">{{ __('Logs') }}</span>
                        </a>
                    </li>
                    @elsecan(UserPermission::VIEW_LOGS)
                    <li class="list-inline-item px-0">
                        <a class="nav-link" href="{{ route('logs.show') }}">
                            <span class="font-weight-semibold analogue-2-color">{{ __('Logs') }}</span>
                        </a>
                    </li>
                    @endcan
                </ul>
            </nav>
            <span class="ml-auto pb-4 pb-md-0"><small class="primary-color-a4">{{ __('versione') }}: {{ config('app.version') }}</small></span>
        </div>
    </div>
</footer>
<!-- End Footer -->
