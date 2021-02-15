@extends('layouts.page', ['graphicBackground' => true])

@section('title', __('Siti web'))
@section('title-description')
    @include('pages.websites.partials.callout_custom')
@endsection

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @if (session()->has('tenant_id') || $authUser->can(UserPermission::ACCESS_ADMIN_AREA))
        @include('partials.datatable')
        <div class="show-when-active mt-4 text-center text-sm-left{{ $authUser->cannot(UserPermission::MANAGE_WEBSITES) ? ' d-none' : '' }}">
        @component('layouts.components.link_button', [
            'icon' => 'it-plus',
            'link' => $websiteCreateUrl,
            'size' => 'lg',
        ])
        {{ __('aggiungi un sito web') }}
        @endcomponent
        </div>
        @if ($authUser->cannot(UserPermission::MANAGE_WEBSITES) && $authUser->status->is(UserStatus::PENDING))
            <div class="hide-when-active alert alert-info mt-5" role="alert">
                {{ __("Nuovi :elements potranno essere aggiunti solo dopo l'attivazione del sito istituzionale.", [
                    'elements' => __('siti')
                ]) }}
                <br>
                {!! __("L'attivazione può essere verificata manualmente usando l'icona 'verifica attivazione' :icon.", [
                    'icon' => '<svg class="icon icon-primary icon-sm"><use xlink:href="' . asset('svg/sprite.svg#it-plug') . '"></use></svg>'
                ]) !!}
                @if ((!app()->environment('production') && config('wai.custom_public_administrations', false)))
                    <br><br>
                    {!! __(":nb: in questo ambiente, l'attivazione può essere forzata mediante l'icona 'attivazione forzata' :icon.", [
                        'nb' => '<strong>' . __('Nota bene') . '</strong>',
                        'icon' => '<svg class="icon icon-warning icon-sm"><use xlink:href="' . asset('svg/sprite.svg#it-plug') . '"></use></svg>'
                    ]) !!}
                @endif
            </div>
        @endif
    @else
        @include('pages.websites.partials.add_primary', ['customForm' => false])
    @endif
    @endcomponent
@endsection
