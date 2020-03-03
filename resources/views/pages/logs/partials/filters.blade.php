<div id="log-filters">
    <h5 class="section-header">{{ __('ricerca e filtri') }}</h5>
    @can(UserPermission::ACCESS_ADMIN_AREA)
    <input type="hidden" id="ipa_code" name="ipa_code" maxlength="25" value="{{ session('super_admin_tenant_ipa_code') }}">
    <div id="public-administration-selected" class="alert alert-info alert-dismissible rounded fade show{{ session()->has('super_admin_tenant_ipa_code') ? '' : ' d-none' }}" role="alert">
        {{ __('Stai visualizzando i messaggi log relativi alla pubblica amministrazione selezionata nella barra di navigazione.') }}<br>
        {!! __('In alternativa puoi :show_all_logs', ['show_all_logs' => '<a href="#" id="reset-ipa_code-filter">' . __('visualizzare i log relativi a tutte le pubbliche amministrazioni') . '</a>.']) !!}
    </div>
    <div id="public-administration-not-selected" class="alert alert-info alert-dismissible rounded fade show{{ session()->has('super_admin_tenant_ipa_code') ? ' d-none' : '' }}" role="alert">
        {{ __('Stai visualizzando i log relativi tutto il portale :app.', ['app' => config('app.name')]) }}<br>
        {{ __('Se vuoi limitare la ricerca ad una sola amministrazione puoi selezionarla nella barra di navigazione in alto.') }}
    </div>
    @endcan
    <div class="form-row pt-5">
        <div class="col-md-4">
            <div class="it-datepicker-wrapper">
                <div class="form-group">
                    <input type="text" class="form-control it-date-datepicker" name="start_date" id="start_date" value="{{ old('start_date') }}" maxlength="50">
                    <label for="start_date">{{ __('Data di inizio') }}</label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-2">
            <input type="time" class="form-control" name="start_time" id="start_time" value="{{ old('start_time') }}">
            <label for="start_time">{{ __('Ora di inizio') }}</label>
            <div class="invalid-feedback"></div>
        </div>
        <div class="col-md-4">
            <div class="it-datepicker-wrapper">
                <div class="form-group">
                    <input type="text" class="form-control it-date-datepicker" name="end_date" id="end_date" value="{{ old('end_date') }}" maxlength="50">
                    <label for="end_date">{{ __('Data di fine') }}</label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-2">
            <input type="time" class="form-control" name="end_time" id="end_time" value="{{ old('end_time') }}">
            <label for="end_time">{{ __('Ora di fine') }}</label>
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-8">
            <label for="message">{{ __('Contenuto del messaggio (almeno 3 caratteri)') }}</label>
            <input type="text" class="form-control" name="message" id="message" value="{{ old('message') }}" maxlength="255">
            <div class="invalid-feedback"></div>
        </div>
        <div class="form-group col-md-4">
            <div class="bootstrap-select-wrapper flex-grow-1">
                <label>{{ __('Livello minimo') }}</label>
                <select title="{{ __('qualunque') }}" id="severity" name="severity">
                    <option value="" title="{{ __('qualunque') }}" data-content="{{ __('qualunque') }} <span class='reset-label'></span>"></option>
                    @foreach(Logger::getLevels() as $label => $value)
                    @if ($value >= Logger::INFO || $currentUser->isA(UserRole::SUPER_ADMIN))
                    <option value="{{ $value }}" {{ old('severity') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-4">
            <div class="bootstrap-select-wrapper flex-grow-1">
                <label>{{ __('Tipo di evento') }}</label>
                <select title="{{ __('qualunque') }}" id="event" name="event" data-live-search="true" data-live-search-placeholder="Cerca..." aria-disabled="false">
                    <option value="" title="{{ __('qualunque') }}" data-content="{{ __('qualunque') }} <span class='reset-label'></span>"></option>
                    @foreach(EventType::toSelectArray() as $value => $label)
                    <option value="{{ $value }}" {{ old('event') === $value ? 'selected' : '' }}{!! (EventType::EXCEPTION()->value === $value) ? ' data-type="exception"' : '' !!}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-4">
            <div class="bootstrap-select-wrapper flex-grow-1 disabled">
                <label>{{ __('Tipo di errore') }}</label>
                <select title="{{ __('qualunque') }}" id="exception_type" name="exception_type" data-live-search="true" data-live-search-placeholder="Cerca..." disabled aria-disabled="true">
                    <option value="" title="{{ __('qualunque') }}" data-content="{{ __('qualunque') }} <span class='reset-label'></span>"></option>
                    @foreach(ExceptionType::toSelectArray() as $value => $label)
                    <option value="{{ $value }}" {{ old('exception') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group col-md-4">
            <div class="bootstrap-select-wrapper flex-grow-1">
                <label>{{ __('Tipo di attività') }}</label>
                <select title="{{ __('qualunque') }}" id="job" name="job" data-live-search="true" data-live-search-placeholder="Cerca..." aria-disabled="false">
                    <option value="" title="{{ __('qualunque') }}" data-content="{{ __('qualunque') }} <span class='reset-label'></span>"></option>
                    @foreach(JobType::toSelectArray() as $value => $label)
                    <option value="{{ $value }}" {{ old('job') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <div class="input-group">
                <label for="website_id">{{ __('Sito web') }}</label>
                <input type="search" autocomplete="off" class="form-control autocomplete" id="website_id" name="website_id" data-search="searchWebsites" data-source="{{ $searchWebsitesEndpoint }}" value="{{ old('website_id') }}" maxlength="50">
                <ul class="autocomplete-list"></ul>
                <div class="searching-icon input-group-append">
                    <div class="input-group-text">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-settings') }}"></use></svg>
                    </div>
                </div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="input-group">
                <label for="user_uuid">{{ __('Utente') }}</label>
                <input type="search" autocomplete="off" class="form-control autocomplete" id="user_uuid" name="user_uuid" data-search="searchUsers" data-source="{{ $searchUsersEndpoint }}" value="{{ old('user_uuid') }}" maxlength="50">
                <ul class="autocomplete-list"></ul>
                <div class="searching-icon input-group-append">
                    <div class="input-group-text">
                        <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-settings') }}"></use></svg>
                    </div>
                </div>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
</div>
