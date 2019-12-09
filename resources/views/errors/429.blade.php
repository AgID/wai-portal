@extends('layouts.page_bulk')

@section('title', __('Troppe richieste'))

@section('title-after')
<svg class="icon icon-xl icon-danger mb-1 ml-2"><use xlink:href="{{ asset('svg/sprite.svg#it-error') }}"></use></svg>
@endsection

@section('content')
<p class="lead text-primary font-weight-semibold">{{ __('Spiacenti, sono pervenute troppe richieste.') }}</p>
<p><a href="{{ route('home') }}">{{ ucfirst(__('torna alla pagina iniziale')) }}</a>.</p>
@endsection
