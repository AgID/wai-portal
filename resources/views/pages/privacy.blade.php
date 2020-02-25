@extends('layouts.page')

@section('title', __('Informativa sul trattamento dei dati personali'))

@section('content')
<div class="mb-5">
    @markdown($privacy)
</div>
@endsection
