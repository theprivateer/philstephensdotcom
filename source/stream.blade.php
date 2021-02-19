---
title: Stream
description: The stream
pagination:
    collection: stream
    perPage: 20
---
@extends('_layouts.master')

@section('body')
    <div class="lg:w-2/3 md:w-3/4">
        <h1 class="font-serif">Stream</h1>

        <hr class="border-b my-6">

        @foreach ($pagination->items as $post)
            <div class="flex flex-col mb-4" id="post-{{ $post->getFilename() }}">
                <p class="text-gray-700 font-medium my-2">
                    {{ $post->getDate()->format('F j, Y') }}
                </p>

                @if(count($post->images) == 1)
                    <img src="{{ $post->images[0] }}" alt="">
                @elseif(count($post->images) > 1)
                    @include('_components.slideshow', ['images' => $post->images])
                @endif

                <div class="mb-4 mt-0">{!! $post->getContent() !!}</div>
            </div>

            @if ($post != $pagination->items->last())
                <hr class="border-b my-6">
            @endif
        @endforeach

        @include('_components.simple-paginator')
    </div>
@stop