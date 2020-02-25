@extends('layouts.page')

@section('title', __('Note legali'))

@section('content')
<div class="my-5">
    @markdown($legal)
</div>
@endsection
