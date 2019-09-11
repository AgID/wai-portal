@extends('layouts.page_bulk')

@section('title', __("Errore dell'applicazione"))

@section('title-after')
<svg class="icon icon-xl icon-danger mb-1 ml-2"><use xlink:href="{{ asset('svg/sprite.svg#it-error') }}"></use></svg>
@endsection

@section('content')
<p class="lead text-primary font-weight-semibold">
    {{ __('Si è verificato un errore inaspettato.') }}<br>
    {{ __('Se dovesse ripetersi ti preghiamo di contattarci.') }}
</p>
<p class="text-primary my-4">{{ $exception->getMessage() }}</p> {{-- TODO: remove in production --}}
<p><a href="{{ route('home') }}">{{ __('Torna alla pagina iniziale') }}</a>.</p>
@endsection
