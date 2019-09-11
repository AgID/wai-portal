@extends('layouts.page', ['fullWidth' => true])

@section('title', __('Siti web'))

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        @component('layouts.components.box', ['classes' => 'rounded'])
        @if (session()->has('tenant_id') || auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.datatable')
        @can(UserPermission::ACCESS_ADMIN_AREA)
        @include('partials.link_button', [
            'label' => __('Aggiungi sito web'),
            'icon' => 'it-plus',
            'link' => $websiteCreateUrl,
            'size' => 'lg',
        ])
        @endcan
        @else
        @include('pages.websites.partials.add_primary')
        @endif
        @endcomponent
    </div>
</div>
@cannot(UserPermission::ACCESS_ADMIN_AREA)
<div id="create-websites" class="container py-5{{ auth()->user()->cannot(UserPermission::MANAGE_WEBSITES) ? ' d-none' : '' }}">
    <div class="row">
        <div class="col-md-6">
            <h5 class="section-header">{{ __('Aggiungi altri siti web') }}</h5>
            <p class="mb-5">
                {{ __("È possibile aggiungere altri siti web connessi alla tua amministrazione come ad esempio i siti tematici e le piattaforme di servizi.") }}
            </p>
                @include('partials.link_button', [
                    'label' => __('Aggiungi sito'),
                    'icon' => 'it-plus',
                    'link' => $websiteCreateUrl,
                    'size' => 'lg',
                ])
        </div>
        <div class="col-md-6 text-center d-none d-md-block">
            <img src="https://placeholder.pics/svg/180">
        </div>
    </div>
</div>
@endcannot
@endsection
