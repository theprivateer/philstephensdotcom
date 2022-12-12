@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        @yield('content')
    </article>

    @include('_partials.up-next')
@endsection

@section('head')
    <link rel="stylesheet" href="{{ $page->baseUrl . '/assets/build/js/styles/atom-one-light.min.css' }}">
@endsection

@section('scripts')
    <script src="{{ $page->baseUrl . '/assets/build/js/highlight.min.js' }}"></script>
    <script>
        hljs.highlightAll();
    </script>
@endsection
