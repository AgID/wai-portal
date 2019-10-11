<div class="user-dropdown dropdown">
    <a class="btn btn-dropdown dropdown-toggle" role="button" id="userDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span>{{ __('Ciao,') }} {{ $authUser->name }}!</span>
        <svg class="icon-expand icon icon-sm icon-primary"><use xlink:href="{{ asset('svg/sprite.svg#it-expand') }}"></use></svg>
    </a>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdownButton">
        <div class="link-list-wrapper">
            <ul class="link-list">
                <li>
                    <div class="avatar-wrapper avatar-extra-text px-4">
                        <div class="avatar size-lg">
                            <img src="https://www.gravatar.com/avatar/{{ md5(strtolower(trim($authUser->email))) }}?d=identicon" alt="{{ $authUser->full_name }}">
                        </div>
                        <div class="extra-text">
                            <h4>{{ $authUser->full_name }}</h4>
                            <p class="small"><small>{{ $authUser->all_role_names }}</small></p>
                        </div>
                    </div>
                </li>
                <li><span class="divider"></span></li>
                @if(!$authUser->status->is(UserStatus::INVITED))
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('admin.user.profile.edit') }}">
                        <span>{{ __('Profilo') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-user') }}"></use></svg>
                    </a>
                </li>
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('admin.password.change.show') }}">
                        <span>{{ __('Cambio password') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-key') }}"></use></svg>
                    </a>
                </li>
                @endif
                <li>
                    <a class="text-primary right-icon list-item" href="{{ route('admin.logout') }}">
                        <span>{{ __('Disconnetti') }}</span>
                        <svg class="icon icon-sm icon-primary right"><use xlink:href="{{ asset('svg/sprite.svg#it-unlocked') }}"></use></svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

{{-- <ul>
    <li>
        <a href="{{ route('admin.dashboard') }}">
            Dashboard amministrativa
        </a>
    </li>
    <li>
        <a href="{{ route('admin.users.index') }}">
            Gestione utenti amministratori
        </a>
    </li>
</ul>
</li> --}}
