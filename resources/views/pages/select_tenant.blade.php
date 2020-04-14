@extends('layouts.page_no_menu')

@section('title', __('Seleziona pubblica amministrazione'))

@section('content')
    <div class="mb-5">
        @component('layouts.components.box', ['classes' => 'rounded'])
            <div class="col-lg-12 ml-auto">
                @include('layouts.includes.public_administration_selector')
            </div>
        @endcomponent
        @if ($hasTenant)
        <div class="mt-4 text-center text-sm-left">
            @component('layouts.components.link_button', [
                'link' => route('home'),
                'size' => 'lg',
            ])
            {{ __('Continua') }}
            @endcomponent
        </div>
        @endif
    </div>
@endsection
