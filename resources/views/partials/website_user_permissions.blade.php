<div class="Form-field {{ $errors->has('usersPermissions') ? 'is-invalid' : '' }}">
    @error('usersPermissions')
    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
        <p class="u-text-p u-padding-r-bottom">{{ $errors->first('usersPermissions') }}</p>
        @enderror
        <label class="Form-label is-required" for="usersPermissions">
            Permessi{{-- //TODO: put message in lang file --}}
        </label>
        @include('partials.datatable')
        @error('usersPermissions')
    </div>
    @enderror
</div>
