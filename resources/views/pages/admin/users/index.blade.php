@extends('layouts.page', ['fullWidth' => true])

@section('title', __('Utenti super amministratori'))

@section('page-inner-container')
<div class="lightgrey-bg-a1">
    <div class="container py-5">
        @parent
        @component('layouts.components.box', ['classes' => 'rounded'])
        @include('partials.datatable')
        @include('partials.link_button', [
            'label' => __('Aggiungi utente'),
            'icon' => 'it-plus',
            'link' => route('admin.users.create'),
            'size' => 'lg',
        ])
        @endcomponent
    </div>
</div>
@endsection
