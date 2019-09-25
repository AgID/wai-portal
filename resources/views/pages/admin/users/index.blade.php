@extends('layouts.page')

@section('title', __('Utenti super amministratori'))

@section('content')
    @component('layouts.components.box', ['classes' => 'rounded'])
    @include('partials.datatable')
    <div class="mt-4 text-center text-sm-left">
    @component('layouts.components.link_button', [
        'icon' => 'it-plus',
        'link' => route('admin.users.create'),
        'size' => 'lg',
    ])
        {{ __('Aggiungi utente') }}
    @endcomponent
    </div>
    @endcomponent
@endsection
