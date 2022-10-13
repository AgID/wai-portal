@extends('layouts.page')

@section('title', __('Aggiungi un sito web'))

@section('content')
@unless(config('wai.app_suspended'))
@include('pages.websites.partials.form', ['route' => $websiteStoreUrl])
@else
<div class="alert alert-info my-3" role="alert">
    {{ __('Al momento non è possibile aggiungere nuovi siti web a :app.', ['app' => config('app.name')]) }}
</div>
@endunless
@endsection
