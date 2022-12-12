@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        @yield('content')
    </article>
@endsection
