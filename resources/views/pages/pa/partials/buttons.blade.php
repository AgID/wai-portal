<div class="mt-12 text-sm-center">
    <div class="d-md-flex justify-content-start">
        @if($showSelectButton)
        <a role="button" class="btn btn-sm btn-icon btn-outline-primary my-2 mx-2"
            href="{{ $isSuperAdmin ? route('admin.publicAdministrations.select') : route('publicAdministrations.select') }}">
            {{ __('Seleziona un\'amministrazione') }}
            <svg class="icon icon-primary ml-2 align-middle">
                <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
            </svg>
        </a>
        @endif
        @if($showInvitedButton && !$isSuperAdmin)
        <a role="button" class="btn btn-sm btn-icon btn-outline-primary my-2 mx-2"
            href="{{ route('publicAdministrations.show') }}">
            {{ __('Le tue amministrazioni') }}
            <svg class="icon icon-primary ml-2 align-middle">
                <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
            </svg>
        </a>
        @endif
        @if($showAddButton)
        <a role="button" class="btn btn-sm btn-icon btn-outline-primary my-2 mx-2"
            href="{{ $isSuperAdmin ? route('admin.publicAdministrations.add') : route('publicAdministrations.add') }}">
            {{ __('Aggiungi un\'amministrazione') }}
            <svg class="icon icon-primary ml-2 align-middle">
                <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
            </svg>
        </a>
        @endif
        @if($hasTenant  && !$isSuperAdmin )
        <a role="button" class="btn btn-sm btn-icon btn-outline-primary my-2 mx-2"
            href="{{ route('analytics') }}">
            {{ __('Vai alla dashboard') }}
            <svg class="icon icon-primary ml-2 align-middle">
                <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
            </svg>
        </a>
        @endif
    </div>
</div>
