@extends('layouts.page_bulk')

@section('title', __('Pagina non trovata'))

@section('title-after')
<svg class="icon icon-xl icon-warning mb-1 ml-2"><use xlink:href="{{ asset('svg/sprite.svg#it-warning-circle') }}"></use></svg>
@endsection

@section('content')
<p class="display-1 text-primary">404</p>
<p class="lead text-primary font-weight-semibold">{!! __('La pagina :page non esiste.', ['page' => '<code>' . mb_strimwidth(request()->path(), 0, 50, "...") . '</code>']) !!}</p>
<p><a href="{{ route('home') }}">{{ ucfirst(__('torna alla pagina iniziale')) }}</a>.</p>
@endsection
