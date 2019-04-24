@extends('layouts.default')

@section('title', __('ui.pages.profile.show.title'))

@section('content')
    @include('partials.user_info')
    <div class="Grid Prose u-layout-prose u-textBreak u-margin-bottom-xl">
        <div class="Grid-cell u-size1of3">Nome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->name }}</div>
        <div class="Grid-cell u-size1of3">Cognome</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->familyName }}</div>
        <div class="Grid-cell u-size1of3">Indirizzo email</div>
        <div class="Grid-cell u-size2of3 u-textWeight-600">{{ $user->email }}</div>
    </div>
    <a role="button" href="{{ route($user->isA(UserRole::SUPER_ADMIN) ? 'admin.profile.edit' : 'user.profile.edit') }}" class="Button Button--default u-text-xs submit">
        Modifica{{-- //TODO: put message in lang file --}}
    </a>
@endsection
