@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Utenti'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @include('partials.datatable')
    @if ($authUser->can(UserPermission::MANAGE_USERS) || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
    <div class="mt-4 text-center text-sm-left">
    @component('layouts.components.link_button', [
        'icon' => 'it-plus',
        'link' => $userCreateUrl,
        'size' => 'lg',
    ])
    {{ __('aggiungi utente') }}
    @endcomponent
    </div>
    @endif
    @endcomponent
@endsection
