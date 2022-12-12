{{--<section class="subscribe">--}}
{{--    <h3>Where do you go from here?</h3>--}}
{{--    <p>Follow via <a href="/rss.xml">RSS</a> or <a href="https://buttondown.email/philstephens" target="_blank">Email</a>. Thoughts? Comments? <a href="/contact">Get in touch</a>.</p>--}}
{{--</section>--}}

@if ($previous = $page->getPrevious())
    <div class="pill">
        <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}">
            Next Article: {{ $previous->title }}
        </a>
    </div>
@endif
