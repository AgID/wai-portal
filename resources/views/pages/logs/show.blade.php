@extends('layouts.default')

@section('title', __('ui.pages.logs.title'))

@section('content')
    <form id="filters" class="Form Form--spaced u-text-r-xs" method="post" action="" data-show-pa="{{ $currentUser->isA(UserRole::SUPER_ADMIN) }}">
        @csrf
        <fieldset class="Form-fieldset">
            <legend class="Form-legend">{{ __('ui.pages.logs.form.legend') }}</legend>

                @error('start_date')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="start_date">{{ __('ui.pages.logs.form.inputs.start_date.label') }}</label>
                    <div class="Grid Grid--alignMiddle Grid--fit Grid--withGutter">
                        <div class="Grid-cell u-size10of12">
                            <input class="Form-input u-text-r-s u-borderRadius-m js-Datepicker" type="text" name="start_date" id="start_date" value="{{ old('start_date') }}" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])\/(0?[1-9]|1[012])\/([0-9]{4})">
                        </div>
                        <div class="Grid-cell u-size2of12">
                            <button type="button" aria-describedby="start_date-label" aria-controls="start_date">
                                <span class="u-hiddenVisually">{{ __('ui.pages.logs.form.inputs.start_date.button') }}</span>
                                <span class="Icon-calendar u-text-r-l"></span>
                            </button>
                        </div>
                    </div>
                    @error('start_date')
                </div>
                @enderror

                @error('start_time')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <div class="Form-field Form-field--time Grid-cell">
                        <label class="Form-label" for="start_time">{{ __('ui.pages.logs.form.inputs.start_time.label') }}</label>
                        <input class="Form-input u-text-r-s u-borderRadius-m" type="text" id="starttime" name="start_time" value="{{ old('start_time') }}" pattern="(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])">
                    </div>
                    @error('start_time')
                </div>
                @enderror

                @error('end_date')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="end_date">{{ __('ui.pages.logs.form.inputs.end_date.label') }}</label>
                    <div class="Grid Grid--alignMiddle Grid--fit Grid--withGutter">
                        <div class="Grid-cell u-size10of12">
                            <input class="Form-input u-text-r-s u-borderRadius-m js-Datepicker" type="text" name="end_date" id="end_date" value="{{ old('end_date') }}" pattern="(0[1-9]|1[0-9]|2[0-9]|3[01])\/(0?[1-9]|1[012])\/([0-9]{4})">
                        </div>
                        <div class="Grid-cell u-size2of12">
                            <button type="button" aria-describedby="end_date-label" aria-controls="end_date">
                                <span class="u-hiddenVisually">{{ __('ui.pages.logs.form.inputs.end_date.button') }}</span>
                                <span class="Icon-calendar u-text-r-l"></span>
                            </button>
                        </div>
                    </div>
                    @error('start_date')
                </div>
                @enderror

                @error('end_time')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <div class="Form-field Form-field--time Grid-cell">
                        <label class="Form-label" for="end_time">{{ __('ui.pages.logs.form.inputs.end_time.label') }}</label>
                        <input class="Form-input u-text-r-s u-borderRadius-m" type="text" id="endtime" name="end_time" value="{{ old('end_time') }}" pattern="(0[0-9]|1[0-9]|2[0-3]):(0[0-9]|1[0-9]|2[0-9]|3[0-9]|4[0-9]|5[0-9])">
                    </div>
                    @error('end_time')
                </div>
                @enderror

            <div class="Form-field">
                <label class="Form-label" for="message">{{ __('ui.pages.logs.form.inputs.message.label') }}</label>
                <input class="Form-input" id="message" name="message" type="text" value="{{ old('message') }}"/>
            </div>

            <div class="Form-field">
                @error('severity')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="severity">{{ __('ui.pages.logs.form.inputs.severity.label') }}</label>
                    <select class="Form-input" id="severity" name="severity">
                        @foreach(Logger::getLevels() as $label => $value)
                            @if ($value >= Logger::INFO || $currentUser->isA(UserRole::SUPER_ADMIN))
                                <option value="{{ $value }}" {{ old('severity') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('severity')
                </div>
                @enderror
            </div>

            <div class="Form-field">
                @error('event')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="event">{{ __('ui.pages.logs.form.inputs.event.label') }}</label>
                    <select class="Form-input" name="event" id="event">
                        <option value="">{{ __('ui.pages.logs.form.inputs.event.empty-selection') }}</option>
                        @foreach(EventType::toSelectArray() as $value => $label)
                            <option value="{{ $value }}" {{ !empty(old('event')) && old('event') == $value ? 'selected' : '' }} @if(EventType::EXCEPTION()->value === $value) type="exception" @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('event')
                </div>
                @enderror
            </div>

            <div class="Form-field">
                @error('type')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="exception">{{ __('ui.pages.logs.form.inputs.exception.label') }}</label>
                    <select class="Form-input" name="exception" id="exception" disabled aria-disabled="true">
                        <option value="">{{ __('ui.pages.logs.form.inputs.exception.empty-selection') }}</option>
                        @foreach(ExceptionType::toSelectArray() as $value => $label)
                            <option value="{{ $value }}" {{ !empty(old('exception')) && old('exception') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                </div>
                @enderror
            </div>

            <div class="Form-field">
                @error('job')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="job">{{ __('ui.pages.logs.form.inputs.job.label') }}</label>
                    <select class="Form-input" name="job" id="job">
                        <option value="">{{ __('ui.pages.logs.form.inputs.job.empty-selection') }}</option>
                        @foreach(JobType::toSelectArray() as $value => $label)
                            <option value="{{ $value }}" {{ !empty(old('job')) && old('job') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('job')
                </div>
                @enderror
            </div>

            @if($currentUser->isA(UserRole::SUPER_ADMIN))
                <div class="Form-field">
                    @error('pa_ipa_code')
                    <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                        <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                        @enderror
                        <label class="Form-label" for="pa">{{ __('ui.pages.logs.form.inputs.pa.label') }}</label>
                        <input class="Form-input autocomplete" type="text" id="pa" name="pa" value="{{ old('pa') }}" data-source="{{ route('admin.logs.search-ipa-list', [], false) }}"/>
                        <input type="hidden" name="pa_ipa_code" value="{{ old('pa_ipa_code') }}"/>
                        @error('pa_ipa_code')
                    </div>
                    @enderror
                </div>
            @endif

            <div class="Form-field">
                @error('website_id')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="website">{{ __('ui.pages.logs.form.inputs.website.label') }}</label>
                    <input class="Form-input autocomplete" type="text" id="website" name="website" value="{{ old('website') }}" data-source="{{ $currentUser->isAn(UserRole::SUPER_ADMIN)? route('admin.logs.search-website', [], false) : route('logs.search-website', [], false) }}"/>
                    <input type="hidden" name="website_id" value="{{ old('website_id') }}"/>
                    @error('website_id')
                </div>
                @enderror
            </div>

            <div class="Form-field">
                @error('user_uuid')
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $message }}</p>
                    @enderror
                    <label class="Form-label" for="user">{{ __('ui.pages.logs.form.inputs.user.label') }}</label>
                    <input class="Form-input autocomplete" type="text" id="user" name="user" value="{{ old('user') }}" data-source="{{ $currentUser->isAn(UserRole::SUPER_ADMIN) ? route('admin.logs.search-user', [], false) : route('logs.search-user', [], false) }}"/>
                    <input type="hidden" name="user_uuid" value="{{ old('user_uuid') }}"/>
                    @error('user_uuid')
                </div>
                @enderror
            </div>

        </fieldset>
    </form>

    <table class="LogsDatatable table table-striped responsive u-text-r-xs"
           data-dt-source="{{ json_encode($source) }}"
           data-dt-columns="{{ json_encode($columns) }}"
           data-dt-columns-order="{{ json_encode($columnsOrder) }}">
        <caption class="u-hiddenVisually">{{ $caption }}</caption>
        <thead>
        <tr>
            @foreach (Arr::pluck($columns, 'name') as $column)
                <th scope="col">{{ $column }}</th>
            @endforeach
            <th scope="col"></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <th class="u-text-xxs u-textWeight-200 u-textLeft u-textBreak" style="white-space: normal;">{{ $footer ?? '' }}</th>
            @foreach ($columns as $column)
                <th></th>
            @endforeach
        </tr>
        </tfoot>
    </table>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ mix('/css/datatables.css') }}"/>
@endpush
