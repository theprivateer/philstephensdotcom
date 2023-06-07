@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        <h1>Blog Archive</h1>
    </article>

    <nav class="article-list">
        @foreach($posts->groupBy(function ($item) { return date('Y', $item->date); }) as $key => $year)
            <h4>
                <time>{{ $key }}</time>
            </h4>
            @foreach($year as $post)
                <a href="{{ $post->getUrl() }}">
                    <span>{{ $post->title }}</span>
                    <hr>
                    <time datetime="{{ date('Y-m-d', $post->date) }}">{{ date('F j', $post->date) }}</time>
                </a>
            @endforeach
        @endforeach
    </nav>
@endsection
