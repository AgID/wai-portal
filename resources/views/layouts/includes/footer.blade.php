<!-- Footer -->
<footer class="it-footer">
    <div class="it-footer-main">
        <div class="container">
            <section>
                <div class="row clearfix align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="it-brand-wrapper">
                            <a class="d-inline-flex" href="{{ url('/') }}">
                                <img alt="logo" class="icon" src="{{ asset(config('site.logo')) }}">
                                <div class="it-brand-text">
                                    <h2>{{ config('app.name') }}</h2>
                                    <h3>{{ __('I dati dei siti web della PA') }}</h3>
                                </div>
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
            </section>
        </div>
    </div>
    <div class="it-footer-small-prints clearfix">
        <div class="container d-flex align-items-end align-items-md-center">
            <h3 class="sr-only">{{ __('navigazione secondaria') }}</h3>
            <ul class="it-footer-small-prints-list list-inline mb-0 d-flex flex-column flex-md-row pl-0">
                @foreach (config('site.footer_links') as $footerMenuItem)
                <li class="list-inline-item px-0">
                    <a class="nav-link" href="{{ route($footerMenuItem['route']) }}">
                        <span class="font-weight-semibold analogue-2-color">{{ __($footerMenuItem['name']) }}</span>
                    </a>
                </li>
                @endforeach
                @can(UserPermission::ACCESS_ADMIN_AREA)
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
            <span class="ml-auto pb-4 pb-md-0"><small class="primary-color-a4">{{ __('versione') }}: {{ config('app.version') }}</small></span>
        </div>
    </div>
</footer>
<!-- End Footer -->
