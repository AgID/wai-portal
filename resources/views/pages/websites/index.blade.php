@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Siti web'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @if (session()->has('tenant_id') || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
    @include('partials.datatable')
    @if ($authUser->can(UserPermission::MANAGE_WEBSITES) || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
    <div class="show-when-active mt-4 text-center text-sm-left{{ $authUser->cannot(UserPermission::MANAGE_WEBSITES) ? ' d-none' : '' }}">
    @component('layouts.components.link_button', [
        'icon' => 'it-plus',
        'link' => $websiteCreateUrl,
        'size' => 'lg',
    ])
        {{ __('Aggiungi sito web') }}
    @endcomponent
    </div>
    @endif
    @else
    @include('pages.websites.partials.add_primary')
    @endif
    @endcomponent
@endsection
