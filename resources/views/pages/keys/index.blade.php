@extends('layouts.page', ['graphicBackground' => true])

@section('title', "Chiavi OAuth")

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        @if (session()->has('tenant_id') || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.datatable')
        <div class="show-when-active mt-4 text-center text-sm-left{{ $authUser->cannot(UserPermission::MANAGE_WEBSITES) ? ' d-none' : '' }}">
            @component('layouts.components.link_button', [
                'icon' => 'it-plus',
                'link' => $newKeyUrl,
                'size' => 'lg',
            ])
            {{ __('aggiungi chiave') }}
            @endcomponent
            </div>
        @endcomponent
        @endif
    </div>
</div>
@endsection
