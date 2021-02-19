@extends('_layouts.master')

@php
    $page->type = 'article';
@endphp

@section('body')
    <h1 class="leading-none font-serif mb-2">{{ $page->title }}</h1>

    <p class="text-gray-700 text-xl md:mt-0">{{ date('F j, Y', $page->date) }}</p>

    {{--@if ($page->categories)--}}
        {{--@foreach ($page->categories as $i => $category)--}}
            {{--<a--}}
                {{--href="{{ '/blog/categories/' . $category }}"--}}
                {{--title="View posts in {{ $category }}"--}}
                {{--class="inline-block bg-gray-300 hover:bg-blue-200 leading-loose tracking-wide text-gray-800 uppercase text-xs font-semibold rounded mr-4 px-3 pt-px"--}}
            {{-->{{ $category }}</a>--}}
        {{--@endforeach--}}
    {{--@endif--}}

    <div class="border-b border-blue-200 mb-10 pb-4 post-content">
        @yield('content')
    </div>

    @include('_components.post-navigation')
@endsection
