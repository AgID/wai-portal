@extends('layouts.page')

@section('title', __('FAQ - Domande ricorrenti'))

@section('content')
<div class="text-serif mb-5">{{ __('Naviga per tema, per trovare le risposte che stai cercando. Non riesci a risolvere il tuo dubbio?') }} <a href="{{ route('contacts') }}">{{ __('Scrivici') }}</a>.</div>
<div class="row no-gutters">
    <div class="col-sm-3">
        <div class="faqs-sidebar sidebar-wrapper">
            <div class="sidebar-linklist-wrapper">
                <div class="link-list-wrapper">
                    <ul class="link-list">
                        <li>
                            <h3>{{ __('Indice dei contenuti') }}</h3>
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
        <div class="faqs-wrapper">
            <div class="faqs collapse-div ml-sm-5" role="tablist">
                @foreach ($faqs as $faq)
                <div id="faq-{{ $loop->iteration }}" class="faq" data-themes="{{ $faq['themes'] }}">
                    <div class="collapse-header" id="faq-{{ $loop->iteration }}-heading">
                        <button class="text-secondary d-flex align-items-center" data-toggle="collapse" data-target="#faq-{{ $loop->iteration }}-body" aria-expanded="false" aria-controls="faq-{{ $loop->iteration }}-body">
                            <span class="mr-auto">{{ $faq['question'] }}</span>
                            @foreach (explode(' ', $faq['themes']) as $theme)
                                <small><span class="badge badge-pill badge-primary py-1 mx-2">{{ ucfirst($theme) }}</span></small>
                            @endforeach
                            <a href="#faq-{{ $loop->iteration }}">
                                <svg class="icon icon-sm"><use xlink:href="{{ asset('svg/sprite.svg#it-link') }}"></use></svg>
                            </a>
                        </button>
                    </div>
                    <div id="faq-{{ $loop->iteration }}-body" class="collapse" role="tabpanel" aria-labelledby="faq-{{ $loop->iteration }}-heading">
                        <div class="collapse-body text-serif">
                        @markdown($faq['answer'])
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
