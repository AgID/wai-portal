@extends('layouts.page')

@section('title', __('FAQ - Domande ricorrenti'))

@section('content')
<div class="text-serif mb-5">{{ __('Naviga per tema, per trovare le risposte che stai cercando. Non riesci a risolvere il tuo dubbio?') }} <a href="{{ route('contacts') }}">{{ __('Scrivici') }}</a>.</div>
<div class="row no-gutters">
    <div class="col-sm-3">
        <div class="faqs-sidebar sidebar-wrapper">
            <div class="form-group px-4">
                <div class="input-group">
                    <label for="faq-search">{{ __('Cerca tra le FAQ') }}</label>
                    <input type="search" class="form-control" id="faq-search" maxlength="255">
                    <div class="input-group-append">
                        <div class="input-group-text"><svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-search') }}"></use></svg></div>
                    </div>
                </div>
            </div>
            <div class="sidebar-linklist-wrapper">
                <div class="link-list-wrapper">
                    <ul class="link-list">
                        <li>
                            <h3>{{ __('Categorie') }}</h3>
                        </li>
                        <li>
                            <button type="button" class="btn btn-icon list-item faq-selector selected" data-theme="all">
                                <span>{{ __('Tutti') }}</span>
                                <svg class="icon icon-primary ml-1">
                                    <use xlink:href="/svg/sprite.svg#it-check"></use>
                                </svg>
                            </button>
                        </li>
                        @foreach ($themes as $theme)
                        <li>
                            <button type="button" class="btn btn-icon list-item faq-selector" data-theme="{{ $theme }}">
                                <span>{{ ucfirst($theme) }}</span>
                                <svg class="icon icon-primary ml-1">
                                    <use xlink:href="/svg/sprite.svg#it-check"></use>
                                </svg>
                            </button>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div id="no-faqs-found" class="d-none ml-sm-5">
            <p class="lead">
                {{ __('Nessun risultato. Prova a usare meno filtri di ricerca.') }}
            </p>
        </div>
        <div class="faqs-wrapper">
            <div class="faqs collapse-div ml-sm-5" role="tablist">
                @foreach ($faqs as $faq)
                <div class="faq collapse-header" id="{{ $faq['id'] ?? Str::slug($faq['question']) }}" role="tab" data-themes="{{ $faq['themes'] }}">
                    <button class="text-secondary d-flex flex-wrap flex-md-nowrap align-items-center" data-toggle="collapse" data-target="#{{ $faq['id'] ?? Str::slug($faq['question']) }}-body" aria-expanded="false" aria-controls="{{ $faq['id'] ?? Str::slug($faq['question']) }}-body">
                        <span class="mr-auto">
                            {{ $faq['question'] }}
                            <a class="faq-anchor" href="#{{ $faq['id'] ?? Str::slug($faq['question']) }}" aria-labelledby="{{ $faq['id'] ?? Str::slug($faq['question']) }}">
                                <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-link') }}"></use></svg>
                            </a>
                        </span>
                        <span class="d-flex flex-wrap flex-md-nowrap flex-md-column align-items-end justify-content-end align-self-end align-self-md-center mt-2 mt-md-0 ml-auto">
                        @foreach (explode(' ', $faq['themes']) as $theme)
                            <small><span class="badge badge-pill badge-primary py-1 mx-2">{{ ucfirst($theme) }}</span></small>
                        @endforeach
                        </span>
                    </button>
                </div>
                <div id="{{ $faq['id'] ?? Str::slug($faq['question']) }}-body" class="collapse faq" aria-labelledby="{{ $faq['id'] ?? Str::slug($faq['question']) }}" data-themes="{{ $faq['themes'] }}">
                    <div class="collapse-body text-serif">
                    @markdown($faq['answer'])
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
