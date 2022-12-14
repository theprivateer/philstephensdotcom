@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        <p><a href="/uses">&larr;</a></p>

        @yield('content')
    </article>
@endsection
