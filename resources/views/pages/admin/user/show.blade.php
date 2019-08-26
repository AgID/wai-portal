@extends('layouts.default')

@section('title', __('ui.pages.admin.users.show.title'))

@section('content')
    @include('partials.user_info')
    {{--TODO: strings in lang file--}}
    <div class="Grid Prose u-layout-prose u-textBreak u-margin-bottom-m">
        <div class="Grid-cell u-size1of3">Nome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->name }}</div>
        <div class="Grid-cell u-size1of3">Cognome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->family_name }}</div>
        <div class="Grid-cell u-size1of3">Indirizzo email</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->email }}</div>
        <div class="Grid-cell u-size1of3">Data creazione</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->created_at->format('d/m/Y') }}</div>
        <div class="Grid-cell u-size1of3">Ultimo aggiornamento</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->updated_at->format('d/m/Y') }}</div>
        <div class="Grid-cell u-size1of3">Ultimo accesso</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ !empty($user->last_access_at) ? $user->last_access_at->format('d/m/Y H:i') : '' }}</div>
    </div>
    @include('partials.link_button', [
        'label' => __('ui.pages.admin.users.index.edit_user'),
        'href' => route('admin.users.edit', ['user' => $user], false)
    ])
@endsection
