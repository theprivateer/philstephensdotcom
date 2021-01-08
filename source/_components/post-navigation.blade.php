<nav class="flex justify-between text-sm md:text-base">
    <div>
        @if ($next = $page->getNext())
            <a href="{{ $next->getUrl() }}" title="Older Post: {{ $next->title }}" class="font-normal">
                &LeftArrow; {{ $next->title }}
            </a>
        @endif
    </div>

    <div class="text-right">
        @if ($previous = $page->getPrevious())
            <a href="{{ $previous->getUrl() }}" title="Newer Post: {{ $previous->title }}" class="font-normal">
                {{ $previous->title }} &RightArrow;
            </a>
        @endif
    </div>
</nav>