@extends('layouts.page', ['graphicBackground' => true])

@section('title', "Chiavi OAuth")

@section('content')
<div class="row">
    <div class="col-lg-12 d-flex">
        @component('layouts.components.box')
        @if (session()->has('tenant_id') || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.datatable')
        @if ($authUser->cannot(UserPermission::MANAGE_WEBSITES))
            <p><strong>{{__('È necessario attivare almeno un sito per accedere alla gestione delle chiavi')}}</strong></p>
        @endif
        <div class="show-when-active mt-4 text-center text-sm-left">
            @component('layouts.components.link_button', [
                'icon' => 'it-plus',
                'link' => $newCredentialUrl,
                'size' => 'lg',
                'disabled' => $authUser->cannot(UserPermission::MANAGE_WEBSITES),
            ])
            {{ __('aggiungi chiave') }}
            @endcomponent
            </div>
        @endcomponent
        @endif
    </div>
</div>
@endsection
