@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        <h1>Dev Articles</h1>

        <p>Not interested in web development? Check out my <a href="/blog">blog</a>.</p>
    </article>

    <nav class="article-list">
        @foreach($dev->groupBy(function ($item) { return date('Y', $item->date); }) as $key => $year)
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
