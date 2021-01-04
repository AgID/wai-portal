@extends('layouts.page')

@section('title', $user->full_name)

@section('title-after')
<small class="text-muted ml-3">{{ __('modifica') }}</small>
@endsection

@section('content')
@include('pages.users.partials.form', [
    'route' => $userUpdateUrl,
    'emailFieldName' => isset($emailPublicAdministrationUser) ? 'emailPublicAdministrationUser' : 'email',
    'emailFieldValue' => isset($emailPublicAdministrationUser) ? $emailPublicAdministrationUser : $user->email,
])
@endsection
