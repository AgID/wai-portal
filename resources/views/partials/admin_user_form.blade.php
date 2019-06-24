<form class="Form Form--spaced u-text-r-xs" method="post" action="{{ $route }}">
    @csrf
    @if (isset($method) && ('POST' !== $method) && ('GET' !== $method))
        @method($method)
    @endif
    @if ($errors->isEmpty())
        <div class="Prose Alert Alert--info">
            <p class="u-text-p">Tutti i campi sono richiesti salvo dove espressamente indicato.</p>
        </div>
    @else
        <div class="Alert Alert--error Alert--withIcon u-margin-r-bottom" role="alert">
            <p class="u-text-p">
                È necessario correggere alcuni errori prima di poter inviare il modulo.
                {{-- //TODO: put message in lang file --}}
            </p>
        </div>
    @endif
    @error('lastAdmin')
    <div class="Alert Alert--error Alert--withIcon u-margin-r-bottom" role="alert">
        <p class="u-text-p">
            {{ $message }}
        </p>
    </div>
    @enderror
    <fieldset class="Form-fieldset">
        <legend class="Form-legend">
            Informazioni dell'utente{{-- //TODO: put message in lang file --}}
        </legend>
        <div class="Form-field {{ $errors->has('name') ? 'is-invalid' : '' }}">
            @error('name')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('name') }}</p>
                    @enderror
                    <label class="Form-label is-required" for="name">
                        Nome{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="name" name="name" type="text" aria-required="true" value="{{ old('name') ?? (isset($user) ? $user->name: '') }}" required>
                    @error('name')
                </div>
            @enderror
        </div>
        <div class="Form-field {{ $errors->has('familyName') ? 'is-invalid' : '' }}">
            @error('familyName')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('familyName') }}</p>
                    @enderror
                    <label class="Form-label is-required" for="familyName">
                        Cognome{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="familyName" name="familyName" type="text" aria-required="true" value="{{ old('familyName') ?? (isset($user) ? $user->familyName : '') }}" required>
                    @error('familyName')
                </div>
            @enderror
        </div>
        <div class="Form-field {{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('email') }}</p>
                    @enderror
                    <label class="Form-label is-required" for="email">
                        Indirizzo email istituzionale{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input" id="email" name="email" type="email" aria-required="true" value="{{ old('email') ?? (isset($user) ? $user->email : '') }}" required>
                    @error('email')
                </div>
            @enderror
        </div>
    </fieldset>
    <div class="Form-field Grid-cell u-textRight">
        <button type="submit" class="Button Button--default u-text-xs">
            Invia{{-- //TODO: put message in lang file --}}
        </button>
    </div>
</form>
