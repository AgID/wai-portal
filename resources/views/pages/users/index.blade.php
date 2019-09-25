@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Utenti'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @include('partials.datatable')
    @if (auth()->user()->can(UserPermission::MANAGE_USERS) || auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA))
    <div class="mt-4 text-center text-sm-left">
    @component('layouts.components.link_button', [
        'icon' => 'it-plus',
        'link' => $userCreateUrl,
        'size' => 'lg',
    ])
        {{ __('Aggiungi utente') }}
    @endcomponent
    </div>
    @endif
    @endcomponent
@endsection
