@extends('layouts.default')

@section('title', __('ui.pages.admin-user_show.title'))

@section('content')
    @include('partials.user_info')
    {{--TODO: strings in lang file--}}
    <div class="Grid Prose u-layout-prose u-textBreak u-margin-bottom-m">
        <div class="Grid-cell u-size1of3">Nome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->name }}</div>
        <div class="Grid-cell u-size1of3">Cognome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->familyName }}</div>
        <div class="Grid-cell u-size1of3">Indirizzo email</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->email }}</div>
    </div>
    @include('partials.link_button', [
        'label' => __('ui.pages.admin-user_show.update'),
        'href' => route('admin-user_edit', ['user' => $user], false)
    ])
@endsection
