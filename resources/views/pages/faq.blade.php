@extends('layouts.page')

@section('title', __('FAQ - Domande ricorrenti'))

@section('content')
<div class="text-serif mb-5">{{ __('Naviga per tema, per trovare le risposte che stai cercando. Non riesci a risolvere il tuo dubbio?') }} <a href="{{ route('contacts', [], false) }}">{{ __('Scrivici') }}</a>.</div>
<div class="row no-gutters">
    <div class="col-sm-3">
        <div class="faqs-sidebar sidebar-wrapper">
            <div class="sidebar-linklist-wrapper">
                <div class="link-list-wrapper">
                    <ul class="link-list">
                        <li>
                            <h3>{{ __('Indice dei contenuti') }}</h3>
                        </li>
                        @foreach ($themes as $theme)
                        <li>
                            <a class="list-item" href="#{{ $theme }}">{{ ucfirst($theme) }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-9">
        <div class="faqs-wrapper">
            <div class="faqs collapse-div ml-5" role="tablist">
                @foreach ($faqs as $faq)
                <div class="faq" data-theme="{{ $faq['theme'] }}">
                    <div class="collapse-header" id="faq-heading-{{ $loop->iteration }}">
                        <button class="text-secondary" data-toggle="collapse" data-target="#faq-body-{{ $loop->iteration }}" aria-expanded="false" aria-controls="faq-body-{{ $loop->iteration }}">
                        {{ $faq['question'] }}
                        </button>
                    </div>
                    <div id="faq-body-{{ $loop->iteration }}" class="collapse" role="tabpanel" aria-labelledby="faq-heading-{{ $loop->iteration }}">
                        <div class="collapse-body">
                        {{ $faq['answer'] }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
