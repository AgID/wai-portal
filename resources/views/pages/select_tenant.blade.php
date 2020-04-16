@extends('layouts.page_bulk')

@section('title', __('Seleziona pubblica amministrazione'))

@section('content')
    <div class="mb-5">

        <p>{{ __("Seleziona l'amministrazione che vuoi gestire.") }}</p>

        <div class="col-lg-12 ml-auto">
            @include('layouts.includes.public_administration_selector')
        </div>

        @if ($hasTenant)
        <div class="mt-4 text-center text-sm-left">
            <p class="text-center">
                <a role="button" class="btn btn-sm btn-icon btn-outline-primary"
                    href="{{ route('analytics') }}">
                    {{ __('Consulta gli analytcs per questa amministrazione') }}
                    <svg class="icon icon-primary ml-2 align-middle">
                        <use xlink:href="{{ asset('svg/sprite.svg#it-arrow-right') }}"></use>
                    </svg>
                </a>
            </p>
        </div>
        @endif
    </div>
@endsection
