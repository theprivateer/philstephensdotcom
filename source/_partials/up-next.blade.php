@if ($previous = $page->getPrevious())
    <div class="pill">
        <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}">
            Next Article: {{ $previous->title }}
        </a>
    </div>
@endif
