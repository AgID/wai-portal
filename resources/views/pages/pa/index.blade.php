@extends('layouts.page_bulk', ['wideLayout' => true])

@section('title', __("Le tue amministrazioni"))

@section('content')
    <div class="mb-3">
        @if($hasPublicAdministrations)
            @include('partials.datatable')
        @else
            <div class="callout mw-100 text-center">
                <p class="mw-100">{{ 'Al momento non appartieni a nessuna amministrazione.' }}</p>
            </div>
        @endif
        <div class="mt-4 text-sm-center">
            <div class="d-md-flex justify-content-start">
                <a role="button" class="btn btn-icon btn-outline-primary my-2 mx-2"
                    href="{{ route('publicAdministrations.add') }}">
                    {{ __("Aggiungi un'amministrazione") }}
                    <svg class="icon icon-primary ml-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg#it-plus') }}"></use>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection
