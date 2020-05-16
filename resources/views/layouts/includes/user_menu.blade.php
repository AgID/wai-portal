<div class="user-dropdown dropdown">
    <a class="btn btn-dropdown dropdown-toggle" role="button" id="userDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span>{{ __('Ciao,') }} {{ $user->name ?? $user->full_name }}!</span>
        <svg class="icon-expand icon icon-sm icon-primary"><use xlink:href="{{ asset('svg/sprite.svg#it-expand') }}"></use></svg>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdownButton">
        <div class="link-list-wrapper">
            <ul class="link-list">
                <li>
                    <div class="avatar-wrapper avatar-extra-text px-4 mb-0">
                        <div class="avatar size-lg">
                            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower($user->email)) }}?d={{ $user->email ? 'identicon' : 'mp' }}" alt="{{ $user->full_name }}">
                        </div>
                        <div class="extra-text">
                            <h4>{{ $user->full_name }}</h4>
                            @auth
                            <p>{{ $user->all_role_names }}</p>
                            @endauth
                        </div>
                    </div>
                </li>
                <li><span class="divider"></span></li>
                @auth
                @if (!$user->status->is(UserStatus::INVITED))
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('user.profile.edit') }}">
                        <span>{{ __('Profilo') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg>
                    </a>
                </li>
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('publicAdministrations.show') }}">
                        <span>{{ __('Amministrazioni') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-pa') }}"></use></svg>
                    </a>
                </li>
                @endif
                @endauth
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('spid-auth_logout') }}">
                        <span>{{ __('Disconnettiti') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-unlocked') }}"></use></svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
