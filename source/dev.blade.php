---
title: Dev
description: Dev stuff
pagination:
    collection: dev
    perPage: 4
---
@extends('_layouts.master')

@section('body')
    <h1 class="font-serif">Dev</h1>

    <hr class="border-b my-6">

    @foreach ($pagination->items as $post)
        @include('_components.post-preview-inline')

        @if ($post != $pagination->items->last())
            <hr class="border-b my-6">
        @endif
    @endforeach

    @include('_components.paginator')
@stop
