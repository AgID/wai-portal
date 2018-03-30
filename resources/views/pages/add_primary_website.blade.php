@extends('layouts.default')

@section('title', __('ui.pages.add-primary-website.title'))

@section('content')
<form class="Form Form--spaced u-text-r-xs" method="post" action="{{ route('public-administrations-store', [], false) }}">
  @csrf
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
  <fieldset class="Form-fieldset">
    <legend class="Form-legend">Informazioni della pubblica amministrazione{{-- //TODO: put message in lang file --}}</legend>
    <div class="Form-field">
      @if ($errors->has('name'))
      <div class="Alert Alert--error Alert--withBg u-padding-r-top u-padding-r-bottom u-padding-r-right">
      <p id="error-name" class="u-text-p u-padding-r-bottom">{{ $errors->first('name') }}</p>
      @endif
      <label class="Form-label is-required" for="name">Nome della pubblica amministrazione di appartenenza{{-- //TODO: put message in lang file --}}</label>
      <input class="Form-input autocomplete" id="name" name="name" aria-required="true" required/>
      @if ($errors->has('name'))</div>@endif
    </div>
    <div class="Form-field">
      <label class="Form-label is-required" for="site">Sito web istituzionale{{-- //TODO: put message in lang file --}}</label>
      <input class="Form-input is-disabled" id="site" name="site" aria-required="true" required readonly>
    </div>
    <div class="Form-field">
      <label class="Form-label is-required" for="pec">Casella PEC istituzionale{{-- //TODO: put message in lang file --}}</label>
      <input class="Form-input is-disabled" id="pec" name="pec" aria-required="true" required readonly>
    </div>
  </fieldset>
  <fieldset class="Form-field Form-field--choose Grid-cell">
    <legend class="Form-legend is-required">Condizioni del servizio</legend>
    <label class="Form-label Form-label--block" for="accept_terms">
      <input type="checkbox" class="Form-input" id="accept_terms" name="accept_terms" aria-required="true" required/>
      <span class="Form-fieldIcon" role="presentation"></span>Accetto le condizioni del servizio
    </label>
  </fieldset>
  <input type="hidden" name="ipa_code" value=""/>
  <div class="Form-field Grid-cell u-textRight">
    <button type="submit" class="Button Button--default u-text-xs">Invia{{-- //TODO: put message in lang file --}}</button>
  </div>
</form>

@endsection

@push('styles')
    <link media="all" rel="stylesheet" href="{{ asset('/css/auto-complete.css') }}"></script>
@endpush

@push('scripts')
    <script type="text/javascript" src="{{ asset('/js/auto-complete.min.js') }}"></script>
    <script type="text/javascript">
      $('input[name="name"]').keypress(function (event) {
          if (event.keyCode == 13) {
              event.preventDefault();
          }
      });
      new autoComplete({
        selector: 'input[name="name"]',
        ipa_code: 'input[name="ipa_code"]',
        site: 'input[name="site"]',
        pec: 'input[name="pec"]',
        minChars: 3,
        source: function(term, suggest) {
          term = term.toLowerCase();
          input = $(this.selector).addClass('autocomplete-loading');
          resetInfo = this.resetInfo;
          $.ajax({
            type: 'POST',
            url: '{{ route('search-ipa-list') }}',
            data: { q: term },
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          }).done(function(data) {
            suggest(data);
            $('.autocomplete-suggestions').hide();
            resetInfo();
          });
          $(document).ajaxStop(function() {
            input.removeClass('autocomplete-loading');
            $('.autocomplete-suggestions').show();
          });
        },
        renderItem: function (item, search) {
          search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
          var re = new RegExp('(' + search.split(' ').join('|') + ')', 'gi');
          return [
            '<div class="autocomplete-suggestion"',
            'data-ipa_code="'+item.ipa_code+'"',
            'data-site="'+item.site+'"',
            'data-pec="'+(item.pec || '')+'"',
            'data-val="'+item.name+'">',
            item.name.replace(re, "<b>$1</b>")+' - '+item.city.replace(re, "<b>$1</b>")+' ('+item.county+')',
            '</div>'
          ].join('');
        },
        onSelect: function(e, term, item) {
          $(this.ipa_code).val(item.getAttribute('data-ipa_code'));
          $(this.site).val(item.getAttribute('data-site'));
          $(this.pec).val(item.getAttribute('data-pec'));
        },
        resetInfo: function() {
          $(this.ipa_code).val('');
          $(this.site).val('');
          $(this.pec).val('');
        }
      });
    </script>
@endpush
