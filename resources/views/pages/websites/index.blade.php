@extends('layouts.page', [
    'fullWidth' => true,
    'graphicBackground' => true,
])

@section('title', __('Siti web'))

@section('content')
<div class="container pb-2 pb-sm-3 pb-lg-5">
    @component('layouts.components.box', ['classes' => 'rounded'])
    @if (session()->has('tenant_id') || auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA))
    @include('partials.datatable')
    @can(UserPermission::ACCESS_ADMIN_AREA)
    <div class="mt-4 text-center text-sm-left">
    @component('layouts.components.link_button', [
        'icon' => 'it-plus',
        'link' => $websiteCreateUrl,
        'size' => 'lg',
    ])
        {{ __('Aggiungi sito web') }}
    @endcomponent
    </div>
    @endcan
    @else
    @include('pages.websites.partials.add_primary')
    @endif
    @endcomponent
</div>
@cannot(UserPermission::ACCESS_ADMIN_AREA)
<div id="create-websites" class="py-5{{ auth()->user()->cannot(UserPermission::MANAGE_WEBSITES) ? ' d-none' : '' }}">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5 class="section-header">{{ __('Aggiungi altri siti web') }}</h5>
                <p class="mb-5">
                    {{ __("È possibile aggiungere altri siti web connessi alla tua amministrazione come ad esempio i siti tematici e le piattaforme di servizi.") }}
                </p>
                @component('layouts.components.link_button', [
                    'icon' => 'it-plus',
                    'link' => $websiteCreateUrl,
                    'size' => 'lg',
                ])
                    {{ __('Aggiungi sito') }}
                @endcomponent
            </div>
            <div class="col-md-6 text-center d-none d-md-block">
                <img src="{{ asset('images/add-websites.svg') }}">
            </div>
        </div>
    </div>
</div>
@endcannot
@endsection
