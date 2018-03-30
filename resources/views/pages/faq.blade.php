@extends('layouts.default')

@section('title', __('ui.pages.faq.title'))

@section('content')
    <div class="Accordion Accordion--default fr-accordion js-fr-accordion u-color-grey-30">
        @foreach ($faqs as $faq)
        <div class="Faq" >
            <h2 class="Accordion-header js-fr-accordion__header fr-accordion__header" id="accordion-header-{{ $loop->iteration }}">
                <span class="Accordion-link u-linkClean">
                    {{ $faq['question'] }}
                    <a class="u-color-50 u-textClean u-margin-left-m u-text-s" href="#faq-{{ $loop->iteration }}">
                        <span class="Icon Icon-link"></span>
                    </a>
                </span>
                <span id="faq-{{ $loop->iteration }}"></span>
            </h2>
            <div id="accordion-panel-{{ $loop->iteration }}" class="Accordion-panel fr-accordion__panel js-fr-accordion__panel">
                <div class="Prose u-text-p u-padding-r-bottom u-textSecondary">{{ $faq['answer'] }}</div>
            </div>
        </div>
        @endforeach
    </div>
@endsection
