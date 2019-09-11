@extends('layouts.page', ['fullWidth' => true])

@section('title', __('Utenti'))

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        @component('layouts.components.box', ['classes' => 'rounded'])
        @include('partials.datatable')
        @if (auth()->user()->can(UserPermission::MANAGE_USERS) || auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.link_button', [
            'label' => __('Aggiungi utente'),
            'icon' => 'it-plus',
            'link' => $userCreateUrl,
            'size' => 'lg',
        ])
        @endif
        @endcomponent
    </div>
</div>
@endsection
