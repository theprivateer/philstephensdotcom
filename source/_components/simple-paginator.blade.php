@if ($pagination->pages->count() > 1)
    <nav class="flex text-base my-8 justify-between">
        @if ($previous = $pagination->previous)
            <a
                    href="{{ $previous }}"
                    title="Previous Page"
                    class="border border-gray-400 text-gray-700 hover:bg-gray-400 hover:text-gray-700 rounded mr-3 px-5 py-3"
            >&LeftArrow;</a>
        @else
            <span></span>
        @endif

        @if ($next = $pagination->next)
            <a
                    href="{{ $next }}"
                    title="Next Page"
                    class="border border-gray-400 text-gray-700 hover:bg-gray-400 hover:text-gray-700 rounded mr-3 px-5 py-3"
            >&RightArrow;</a>
        @else
            <span></span>
        @endif
    </nav>
@endif