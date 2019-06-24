<div class="Form-field {{ $errors->has('websitesPermissions') ? 'is-invalid' : '' }}">
    @if ($errors->has('websitesPermissions'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
            <p class="u-text-p u-padding-r-bottom">{{ $errors->first('websitesPermissions') }}</p>
            @endif
            <label class="Form-label is-required" for="websitesPermissions">
                Permessi{{-- //TODO: put message in lang file --}}
            </label>
            @include('partials.datatable')
            @if ($errors->has('websitesPermissions'))
        </div>
    @endif
</div>