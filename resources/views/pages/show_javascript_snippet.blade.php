@extends('layouts.default')

@section('title', __('ui.pages.website-javascript-snippet.title'))

@section('content')
    <pre><code>{{ $javascriptSnippet }}</code></pre>
@endsection
