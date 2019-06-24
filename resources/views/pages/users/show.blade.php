@extends('layouts.default')

@section('title', __('ui.pages.users.show.title'))

@section('content')
    <div class="Grid Prose u-layout-prose u-textBreak u-margin-bottom-xl">
        <div class="Grid-cell u-size4of12">Nome</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->name }}</div>
        <div class="Grid-cell u-size4of12">Cognome</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->familyName }}</div>
        <div class="Grid-cell u-size4of12">Indirizzo email</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->email }}</div>
        <div class="Grid-cell u-size4of12">Status</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->status->description }}</div>
        <div class="Grid-cell u-size4of12">Ruolo</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ UserRole::getDescription($user->roles()->first()->name) }}</div>
        <div class="Grid-cell u-size4of12">Data creazione</div>
        <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->created_at->format('d/m/Y') }}</div>
        @if(!empty($user->email_verified_at))
            <div class="Grid-cell u-size4of12">Data attivazione</div>
            <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->email_verified_at->format('d/m/Y') }}</div>
        @endif
        @if(!empty($user->password_changed_at))
            <div class="Grid-cell u-size4of12">Ultimo cambio password</div>
            <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->password_changed_at->format('d/m/Y') }}</div>
        @endif
        @if(!empty($user->updated_at))
            <div class="Grid-cell u-size4of12">Ultimo aggiornamento</div>
            <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->updated_at->format('d/m/Y') }}</div>
        @endif
        @if(!empty($user->last_access_at))
            <div class="Grid-cell u-size4of12">Ultimo accesso</div>
            <div class="Grid-cell u-size8of12 u-textWeight-600">{{ $user->last_access_at->format('d/m/Y H:i') }}</div>
        @endif
        @includeWhen(!$user->isA(UserRole::ADMIN), 'partials.user_website_permissions')
        @include('partials.link_button', [
            'label' => __('ui.pages.users.index.edit_user'),
            'href' => route('users.edit', ['user' => $user], false)
        ])
    </div>
@endsection
