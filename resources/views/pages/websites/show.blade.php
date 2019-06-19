@extends('layouts.default')

@section('title', __('ui.pages.websites.show.title'))

@section('content')
    <div class="Grid-cell u-size4of12">Nome</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->name }}</div>
    <div class="Grid-cell u-size4of12">URL</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->url }}</div>
    <div class="Grid-cell u-size4of12">Tipo</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->type->description }}</div>
    <div class="Grid-cell u-size4of12">Status</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->status->description }}</div>
    <div class="Grid-cell u-size4of12">Data creazione</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->created_at->format('d/m/Y') }}</div>
    <div class="Grid-cell u-size4of12">Numero di visite negli ultimi 30 giorni:</div>
    <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $lastMonthVisits ?? 'N/A' }}</div>
    @if(!empty($website->updated_at))
        <div class="Grid-cell u-size4of12">Ultimo aggiornamento</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $website->updated_at->format('d/m/Y') }}</div>
    @endif
    @include('partials.website_user_permissions')
    @includeWhen(!$website->type->is(WebsiteType::PRIMARY), 'partials.link_button', [
            'label' => __('ui.pages.websites.index.edit_website'),
            'href' => route('websites.edit', ['website' => $website], false)
        ])
@endsection
