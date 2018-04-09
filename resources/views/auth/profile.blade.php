@extends('layouts.default')

@section('title', __('ui.pages.profile.title'))

@section('content')
    @include('partials.user_info')
    <div class="Grid Prose u-layout-prose u-textBreak">
        <div class="Grid-cell u-size1of3">Nome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->name }}</div>
        <div class="Grid-cell u-size1of3">Cognome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->familyName }}</div>
        <div class="Grid-cell u-size1of3">Indirizzo email</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->email }}</div>
    </div>
@endsection
