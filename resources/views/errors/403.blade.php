@extends('layouts.page_bulk')

@section('title', __('Accesso negato'))

@section('title-after')
<svg class="icon icon-xl icon-danger mb-1 ml-2"><use xlink:href="{{ asset('svg/sprite.svg#it-error') }}"></use></svg>
@endsection

@section('content')
<p class="lead text-primary font-weight-semibold">{{ __('Non hai le autorizzazioni necessarie per accedere alla pagina.') }}</p>
@env('local')
<p class="text-primary my-4">{{ $exception->getMessage() }}</p>
@endenv
@isset($userMessage)
<p class="text-primary my-4">{!! nl2br($userMessage) !!}</p>
@endisset
<p><a href="{{ route('home') }}">{{ __('Torna alla pagina iniziale') }}</a>.</p>
@endsection
