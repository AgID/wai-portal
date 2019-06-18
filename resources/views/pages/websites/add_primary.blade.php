@extends('layouts.default')

@section('title', __('ui.pages.websites.add-primary.title'))

@section('content')
    <form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('websites.store.primary', [], false) }}">
        @csrf
        @if ($errors->isEmpty())
            <div class="Prose Alert Alert--info">
                <p class="u-text-p">
                    Tutti i campi sono richiesti salvo dove espressamente indicato.{{-- //TODO: put message in lang file --}}
                </p>
            </div>
        @else
            <div class="Alert Alert--error Alert--withIcon u-margin-r-bottom" role="alert">
                <p class="u-text-p">
                    È necessario correggere alcuni errori prima di poter inviare il modulo.{{-- //TODO: put message in lang file --}}
                </p>
            </div>
        @endif
        <fieldset class="Form-fieldset">
            <legend class="Form-legend">
                Informazioni della pubblica amministrazione{{-- //TODO: put message in lang file --}}
            </legend>
            <div class="Form-field">
                @if ($errors->has('public_administration_name'))
                <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
                    <p class="u-text-p u-padding-r-bottom">{{ $errors->first('public_administration_name') }}</p>
                @endif
                    <label class="Form-label is-required" for="public_administration_name">
                        Nome della pubblica amministrazione di appartenenza{{-- //TODO: put message in lang file --}}
                    </label>
                    <input class="Form-input autocomplete" id="public_administration_name" name="public_administration_name" aria-required="true" value="{{ old('public_administration_name') }}" required/>
                @if ($errors->has('public_administration_name'))
                </div>
                @endif
            </div>
            <div class="Form-field">
                <label class="Form-label is-required" for="url">
                    Sito web istituzionale{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input is-disabled" id="url" name="url" aria-required="true" value="{{ old('url') }}" required readonly>
            </div>
            <div class="Form-field">
                <label class="Form-label is-required" for="pec">
                    Casella PEC istituzionale{{-- //TODO: put message in lang file --}}
                </label>
                <input class="Form-input is-disabled" id="pec" name="pec" aria-required="true" value="{{ old('pec') }}" required readonly>
            </div>
        </fieldset>
        <fieldset class="Form-field Form-field--choose Grid-cell">
            <legend class="Form-legend is-required">
                Condizioni del servizio{{-- //TODO: put message in lang file --}}
            </legend>
            <label class="Form-label Form-label--block" for="accept_terms">
                <input type="checkbox" class="Form-input" id="accept_terms" name="accept_terms" aria-required="true" required/>
                <span class="Form-fieldIcon" role="presentation"></span>
                Accetto le condizioni del servizio
            </label>
        </fieldset>
        <input type="hidden" name="ipa_code" value="{{ old('ipa_code') }}"/>
        <div class="Form-field Grid-cell u-textRight">
            <button type="submit" class="Button Button--default u-text-xs">
                Invia{{-- //TODO: put message in lang file --}}
            </button>
        </div>
    </form>
@endsection

@push('scripts')
    <script type="text/javascript">
        $('input[name="name"]').keypress(function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
            }
        });
        new autoComplete({
            selector: 'input[name="public_administration_name"]',
            ipa_code: 'input[name="ipa_code"]',
            url: 'input[name="url"]',
            pec: 'input[name="pec"]',
            minChars: 3,
            source: function (term, suggest) {
                term = term.toLowerCase();
                input = $(this.selector).addClass('autocomplete-loading');
                resetInfo = this.resetInfo;
                $.ajax({
                    type: 'GET',
                    url: '{{ route('search-ipa-list') }}',
                    data: {q: term},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).done(function (data) {
                    suggest(data);
                    $('.autocomplete-suggestions').hide();
                    resetInfo();
                });
                $(document).ajaxStop(function () {
                    input.removeClass('autocomplete-loading');
                    $('.autocomplete-suggestions').show();
                });
            },
            renderItem: function (item, search) {
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                var re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
                return [
                    '<div class="autocomplete-suggestion"',
                    'data-ipa_code="' + item.ipa_code + '"',
                    'data-url="' + item.site + '"',
                    'data-pec="' + (item.pec || '') + '"',
                    'data-val="' + item.name + '">',
                    item.name.replace(re, "<b>$1</b>") + ' - ' + item.city.replace(re, "<b>$1</b>") + ' (' + item.county + ')',
                    '</div>'
                ].join('');
            },
            onSelect: function (e, term, item) {
                $(this.ipa_code).val(item.getAttribute('data-ipa_code'));
                $(this.url).val(item.getAttribute('data-url'));
                $(this.pec).val(item.getAttribute('data-pec'));
            },
            resetInfo: function () {
                $(this.ipa_code).val('');
                $(this.url).val('');
                $(this.pec).val('');
            }
        });
    </script>
@endpush
