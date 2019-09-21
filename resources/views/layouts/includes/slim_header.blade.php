<div class="it-header-slim-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="it-header-slim-wrapper-content">
                    <a class="d-none d-lg-block navbar-brand" href="{{ __(config('site.owner.link')) }}">{{ __(config('site.owner.name')) }}</a>
                    <div class="nav-mobile">
                        <nav>
                            <span class="d-lg-none">
                                <a class="d-none d-sm-inline align-middle" href="{{ __(config('site.owner.link')) }}">{{ __(config('site.owner.name')) }}</a>
                                <a class="d-sm-none align-middle" href="{{ __(config('site.owner.link')) }}">{{ __(config('site.owner.name_short')) }}</a>
                                <a class="it-opener d-inline-block p-0 ml-1" data-toggle="collapse" href="#slim-nav" role="button" aria-expanded="false" aria-controls="slim-nav">
                                    <svg class="icon">
                                        <use xlink:href="{{ asset('svg/sprite.svg#it-expand') }}"></use>
                                    </svg>
                                </a>
                            </span>
                            <div class="link-list-wrapper collapse" id="slim-nav">
                                <ul class="link-list border-right-0 px-0">
                                    @foreach (config('site.slim_header_links') as $slimheaderLink)
                                    <li class="{{ $slimheaderLink['class'] ?? '' }}">
                                        <a class="px-3" href="{{ __($slimheaderLink['url']) }}">
                                            {{ __($slimheaderLink['name']) }}
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </nav>
                    </div>
                    <div class="header-slim-right-zone">
                        {{-- Language menu
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false">
                                <span>ITA</span>
                                <svg class="icon d-none d-lg-block">
                                    <use xlink:href="{{ asset('svg/sprite.svg#it-expand') }}"></use>
                                </svg>
                            </a>
                            <div class="dropdown-menu">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="link-list-wrapper">
                                            <ul class="link-list">
                                                <li><a class="list-item" href="#"><span>ITA</span></a></li>
                                                <li><a class="list-item" href="#"><span>ENG</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="it-access-top-wrapper">
                            @if ($spidAuthUser)
                                @auth
                                @include('layouts.includes.user_menu', ['user' => $authUser])
                                @else
                                @include('layouts.includes.user_menu', [
                                    'user' => tap($spidAuthUser, function($spidAuthUser) {
                                        return $spidAuthUser->full_name = implode(' ', [
                                            $spidAuthUser->name,
                                            $spidAuthUser->familyName,
                                        ]);
                                    })
                                ])
                                @endauth
                            @else
                                @can(UserPermission::ACCESS_ADMIN_AREA)
                                @include('layouts.includes.super_admin_menu')
                                @else
                                    @include('spid-auth::spid-button', ['size' => 's'])
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
