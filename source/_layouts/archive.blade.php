@extends('_layouts.master')

@php
    $page->type = 'article';
@endphp

@section('body')
    <p class="bg-orange-500 text-white mb-10 p-4">
        This is an archived post from a previous incarnation of my blog.  It is most likely out of date and with broken links, but I like keeping it around for nostalgic reasons.
    </p>

    <h1 class="leading-none font-serif mb-2">{{ $page->title }}</h1>

    <p class="text-gray-700 text-xl md:mt-0">{{ date('F j, Y', $page->date) }}</p>

    <div class="border-b border-blue-200 mb-10 pb-4 post-content">
        @yield('content')
    </div>

    @include('_components.post-navigation')
@endsection
