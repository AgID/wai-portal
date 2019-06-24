<div class="Form-field Form-field--choose {{ $errors->has('isAdmin') ? 'is-invalid' : '' }}">
    @if ($errors->has('isAdmin'))
        <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
            <p class="u-text-p u-padding-r-bottom">{{ $errors->first('isAdmin') }}</p>
            @endif
            <legend class="Form-legend">Ruolo</legend>
            <label class="Form-label Form-label--block" for="isAdmin">
                <input type="checkbox" class="Form-input" name="isAdmin" id="isAdmin" value="1" {{ old('isAdmin') || (isset($user) && $user->isA(UserRole::ADMIN)) ? 'checked' : '' }}>
                <span class="Form-fieldIcon" role="presentation"></span>Amministratore
            </label>
            @if ($errors->has('isAdmin'))
        </div>
    @endif
</div>
