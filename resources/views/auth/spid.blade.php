@extends('layouts.page_bulk')

@section('title', __('Accesso con SPID'))

@section('content')
@can(UserPermission::ACCESS_ADMIN_AREA)
    <p>{{ __('Per accedere a questa sezione del portale è necessario prima disconnettersi dal proprio account di amministratore.') }}</p>
    <p>
        <a href="{{ route('admin.logout') }}">{{ __('Disconnettiti adesso') }}</a>
        {{ __('oppure') }}
        <a href="{{ route('home') }}">{{ __('torna alla pagina iniziale') }}</a>.
    </p>
@else
    <p>{{ __("La pagina che hai richiesto è raggiungibile solo dopo l'autenticazione.") }}</p>
    <p>
        {{ __("Usa il bottone 'Entra con SPID' che trovi in alto a destra nella pagina.") }}<br>
        <small class="text-muted"><em>{{ __("Se avevi già effettuato l'accesso è probabile che la tua sessione sia scaduta per inattività.") }}</em></small>
    </p>
@endcan
@endsection
