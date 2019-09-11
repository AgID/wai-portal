@extends('layouts.page', ['fullWidth' => true])

@section('title', $website->name)

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        <div class="row">
            <div class="col-md-8 d-flex">
                @component('layouts.components.box')
                <h4 class="text-uppercase m-0">{{ __('informazioni') }}</h4>
                <div class="mt-5 pt-5">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="name" value="{{ $website->name }}" readonly>
                            <label for="name">{{ __('Nome del sito') }}</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="url" value="{{ $website->url }}" readonly>
                            <label for="url">{{ __('URL') }}</label>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="type" value="{{ $website->type->description }}" readonly>
                            <label for="type">{{ __('Tipologia') }}</label>
                        </div>
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="created_at" value="{{ $website->created_at->format('d/m/Y') }}" readonly>
                            <label for="created_at">{{ __('Aggiunto il') }}</label>
                        </div>
                    </div>
                    @isset($website->updated_at)
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="text" class="form-control-plaintext" id="updated_at" value="{{ $website->updated_at->format('d/m/Y') }}" readonly>
                            <label for="updated_at">{{ __('Aggiornato il') }}</label>
                        </div>
                    </div>
                    @endif
                    @includeWhen(auth()->user()->can(UserPermission::MANAGE_WEBSITES), 'partials.link_button', [
                        'label' => __('Modifica'),
                        'icon' => 'it-pencil',
                        'link' => $websiteEditUrl,
                        'size' => 'lg',
                    ])
                </div>
                @endcomponent
            </div>
            <div class="col-md-4 d-flex">
                @component('layouts.components.box')
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="text-uppercase m-0">{{ __('stato') }}</h4>
                    <span class="badge px-3 py-2 website-status {{ strtolower($website->status->key) }}">
                        {{ strtoupper($website->status->description) }}
                    </span>
                </div>
                <div class="box-section lightgrey-bg-a1 my-4">
                    <p>{{ WebsiteStatus::getLongDescription($website->status) }}</p>
                    @if ($website->status->is(WebsiteStatus::PENDING))
                    @can(UserPermission::MANAGE_WEBSITES)
                    <h5 class="section-header">{{ __('attivazione') }}</h5>
                    <p>
                        {{ __('Per impostare il tracciamento, usa il codice che trovi in questa pagina.') }}
                    </p>
                    <p class="text-serif">
                        {{ __("Hai bisogno di aiuto con l'attivazione?") }} <a href={{ route('faq') }}>{{ __('Consulta le FAQ') }}</a>
                    </p>
                    @endcan
                    @elseif (!$website->type->is(WebsiteType::PRIMARY))
                    @can(UserPermission::MANAGE_WEBSITES)
                    @if ($website->status->is(WebsiteStatus::ACTIVE))
                    <h5 class="section-header">{{ __('archiviazione') }}</h5>
                    <p>
                        {{ __('Se non vuoi più tracciare il traffico di questo sito puoi archiviarlo.') }}
                    </p>
                    <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                        href="{{ $websiteArchiveUrl }}"
                        data-type="websiteArchiveUnarchive"
                        data-website-name="{{ $website->name }}"
                        data-current-status-description="{{ $website->status->description }}"
                        data-current-status="{{ $website->status->key }}"
                        aria-disabled="true">
                        {{ __('Archivia') }}
                    </a>
                    @else
                    <h5 class="section-header">{{ __('ripristino') }}</h5>
                    <p>
                        {{ __('Se vuoi di nuovo tracciare il traffico di questo sito puoi ripristinarlo.') }}
                    </p>
                    <a role="button" class="btn btn-sm btn-outline-secondary disabled"
                        href="{{ $websiteUnarchiveUrl }}"
                        data-type="websiteArchiveUnarchive"
                        data-website-name="{{ $website->name }}"
                        data-current-status-description="{{ $website->status->description }}"
                        data-current-status="{{ $website->status->key }}"
                        aria-disabled="true">
                        {{ __('Ripristina') }}
                    </a>
                    @endif
                    @endcan
                    @else
                    <h5 class="section-header">{{ __('sito istituzionale') }}</h5>
                    <p>
                        {{ __('Questo è il sito istituzionale della tua Pubblica Amministrazione.') }}
                    </p>
                    @can(UserPermission::MANAGE_WEBSITES)
                    <p>
                        {{ __('Non è possibile modificare lo stato.') }}
                    </p>
                    @endcan
                    @endif
                </div>
                <div>
                    <p class="text-serif">
                        {{ __('Hai dubbi sul significato dello stato del sito?') }}
                        <a href={{ route('faq') }}>{{ __('Consulta le FAQ') }}</a>
                    </p>
                </div>
                @endcomponent
            </div>
        </div>
        @can(UserPermission::MANAGE_WEBSITES)
        @component('layouts.components.box', ['classes' => 'mt-0 full-width'])
        <div class="javascript-snippet-container blank">
            <div class="collapse-header box-section" id="javascript-snippet-header">
                <button class="px-0" data-toggle="collapse" data-target="#javascript-snippet-collapse" aria-expanded="false" aria-controls="javascript-snippet-collapse">
                    <h4 class="text-uppercase m-0 text-black">{{ __('codice di tracciamento javascript') }}</h4>
                </button>
            </div>
            <div id="javascript-snippet-collapse" class="collapse" role="tabpanel" aria-labelledby="javascript-snippet-header">
                <div class="box-section lightgrey-bg-a1 my-4 mx-0 px-5">
                    <div class="collapse-body">
                        <div id="javascript-snippet" class="lightgrey-bg-a1" data-href="{{ $javascriptSnippetUrl }}">
                            <pre><code></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @cannot(UserPermission::ACCESS_ADMIN_AREA)
        <div class="row box-section">
            <div class="col-md-5">
                @if ($website->status->is(WebsiteStatus::PENDING))
                <h5 class="section-header">{{ __('verifica tracciamento') }}</h5>
                <p>
                    {{ __('Se hai già inserito il codice di tracciamento nel sito') }}
                    <strong>{{ $website->name }}</strong>
                    {{ __('puoi eseguire adesso la verifica e attivare il sito.') }}
                </p>
                <p class="text-center">
                    <a role="button" class="btn btn-sm btn-outline-secondary text-center disabled"
                        href="{{ $websiteTrackingCheckUrl }}"
                        data-type="checkTracking"
                        data-website-name="{{ $website->name }}"
                        aria-disabled="true">
                        {{ __('Verifica adesso') }}
                    </a>
                </p>
                @elseif ($website->status->is(WebsiteStatus::ACTIVE))
                <h5 class="section-header">{{ __('tracciamento attivo') }}</h5>
                <p>
                    {{ __('Il sito') }}
                    <strong>{{ $website->name }}</strong>
                    {{ __('sta già tracciando il traffico! 🎉') }}
                    {!! __('Consulta i dati analytics e prendi <em>decisioni basate sui dati</em> per la tua PA.') !!}
                </p>
                <p class="text-center">
                    <a role="button" class="btn btn-sm btn-outline-secondary"
                        href="{{ route('analytics.service.login') }}">
                        {{ __('Vai agli analytics') }}
                    </a>
                </p>
                @else
                @endif
            </div>
            <div class="col-md-5 offset-md-2">
                <h5 class="section-header">{{ __('opzioni avanzate') }}</h5>
                <p class="text-serif">
                    {{ __('Se vuoi personalizzare il codice di tracciamento per attivare opzioni avanzate') }},
                    <a href="{{ route('faq') }}"> {{ __('consulta la nostra guida') }}</a>.
                </p>
            </div>
        </div>
        @endif
        @endcomponent
        @component('layouts.components.box', ['classes' => 'mt-0'])
        <h4 class="text-uppercase mb-5">{{ __('permessi degli utenti') }}</h4>
        @include('partials.datatable')
        @include('partials.link_button', [
            'label' => __('Modifica'),
            'icon' => 'it-pencil',
            'link' => $websiteEditUrl,
            'size' => 'lg',
        ])
        @endcomponent
        @endcan
        </div>
    </div>
</div>
@endsection
