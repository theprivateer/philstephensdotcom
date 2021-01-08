---
title: Archive
description: The list of archived blog posts for the site
pagination:
    collection: archive
    perPage: 10
---
@extends('_layouts.master')

@section('body')
    <h1 class="font-serif">Archive</h1>

    <p>Here are some of my older posts from previous incarnations of my blog.  Most of them are out of date and with broken links, but I like keeping them around for nostalgic reasons.</p>

    <hr class="border-b my-6">

    @foreach ($pagination->items as $post)
        @include('_components.post-preview-inline', ['hide_excerpt' => true])

        @if ($post != $pagination->items->last())
            <hr class="border-b my-6">
        @endif
    @endforeach

    @include('_components.paginator')
@stop
