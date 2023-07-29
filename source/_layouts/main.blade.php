<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="canonical" href="{{ $page->getUrl() }}">
        <meta name="description" content="{{ $page->getExcerpt(200) }}">
        <link rel="shortcut icon" href="{{ $page->baseUrl }}/assets/img/favicon.png">

        <title>{{ $page->title }}</title>

        <link rel="stylesheet" href="{{ mix('css/style.css', 'assets/build') }}">
        <link rel="alternate" type="application/rss+xml" title="{{ $page->rssTitle }}" href="{{ $page->baseUrl }}/rss.xml" />
        <link rel="micropub" href="{{ $page->baseUrl }}/.netlify/functions/micropub">

        <meta property="og:title" content="{!! $page->title !!}" />
        <meta property="og:description" content="{!! $page->getExcerpt(200) !!}" />
        <meta property="og:type" content="article" />
        <meta property="og:image" content="{{ $page->baseUrl }}/assets/img/social/opengraph-default.png" />
        <meta property="og:url" content="{!! $page->getUrl() !!}" />

        <meta name="twitter:title" content="{!! $page->title !!}" />
        <meta name="twitter:description" content="{!! $page->getExcerpt(200) !!}" />
        <meta name="twitter:site" content="@wattsandhops" />
        <meta name="twitter:image" content="{{ $page->baseUrl }}/assets/img/social/twitter-default.png" />
        @section('head')
        @show
    </head>
    <body>
        <main class="container">
            @yield('body')

            <section class="footer">
            {{--     <ul class="col-25">
                    <li><a href="{{ $page->baseUrl }}/blog">Blog Articles</a></li>
                    <li><a href="{{ $page->baseUrl }}/books">Reading List</a></li>
                </ul>

                <ul class="col-50">
                    <li><a href="{{ $page->baseUrl }}/contact">Contact Me</a></li>
                </ul>

                <ul class="col-25">
                    <li><a href="{{ $page->baseUrl }}/now">Now</a></li>
                    <li><a href="{{ $page->baseUrl }}/uses">Uses</a></li>
                </ul> --}}
            </section>

            <section class="copyright">
                <p>&copy; 2017 - 2022 Phil Stephens</p>
                {{-- <p><a href="{{ $page->baseUrl }}/rss.xml">RSS</a></p> --}}
            </section>
        </main>
        @section('scripts')
        @show

        @if ($page->production)
            <script src="https://tinylytics.app/embed/3xyAFtoF71eNvV9BNxo8.js" defer></script>
        @endif
    </body>
</html>
