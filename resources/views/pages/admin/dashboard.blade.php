@extends('layouts.default')

@section('title', __('ui.pages.admin-dashboard.title'))

@section('content')
    <label class="Form-label is-required" for="type">
        Pubbliche Amministrazioni{{-- //TODO: put message in lang file --}}
    </label>
    <select class="Form-input" id="ipa_code" name="ipa_code" aria-required="true" required>
        <option value="">seleziona</option>{{-- //TODO: use localized enum --}}
        @foreach($publicAdministrations as $publicAdministration)
            <option value="{{ route('admin.publicAdministration.index', ['publicAdministration' => $publicAdministration['ipa_code']]) }}" >{{ $publicAdministration['name'] }}</option>
        @endforeach
    </select>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(document).ready(() => {
            $paSelector = document.getElementById('ipa_code');
            $paSelector.addEventListener('change', (event) => {
                window.location = event.target.value;
            });
        });
    </script>
@endpush