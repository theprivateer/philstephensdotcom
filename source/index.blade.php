---
title: Phil Stephens - cyclist and software developer
---

@extends('_layouts.main')

@section('body')
    @include('_partials.masthead')

    <article class="page-content">
        <h1>Hi, I'm <a>Phil</a></h1>
        <p>I'm a cyclist and software developer - currently a Technical Lead at <a href="https://www.rexsoftware.com">Rex</a>.</p>
        <p>I live in Brisbane, Australia with my wife and two kids and I spend way too much time looking at bikes on the internet.</p>
        {{-- <p>Here is what I'm up to <a href="{{ $page->baseUrl }}/now">at the moment...</a></p> --}}
    </article>

    <nav class="article-list">
        @foreach($blog->take(5) as $post)
            <a href="{{ $post->getUrl() }}">
                <span>{{ $post->title }}</span>
                <hr>
                <time datetime="{{ date('Y-m-d', $post->date) }}">{{ date('F j', $post->date) }}</time>
            </a>
        @endforeach
    </nav>

    <div class="pill">
        <a href="{{ $page->baseUrl }}/blog">See All Blog Articles</a>
    </div>

    <nav class="article-list">
        @foreach($dev->take(5) as $post)
            <a href="{{ $post->getUrl() }}">
                <span>{{ $post->title }}</span>
                <hr>
                <time datetime="{{ date('Y-m-d', $post->date) }}">{{ date('F j', $post->date) }}</time>
            </a>
        @endforeach
    </nav>

    <div class="pill">
        <a href="{{ $page->baseUrl }}/dev">See All Dev Articles</a>
    </div>
@endsection
