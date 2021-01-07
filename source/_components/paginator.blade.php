@if ($pagination->pages->count() > 1)
    <nav class="flex text-base my-8">
        @if ($previous = $pagination->previous)
            <a
                    href="{{ $previous }}"
                    title="Previous Page"
                    class="border border-gray-400 text-gray-700 hover:bg-gray-400 hover:text-gray-700 rounded mr-3 px-5 py-3"
            >&LeftArrow;</a>
        @endif

        @foreach ($pagination->pages as $pageNumber => $path)
            <a
                    href="{{ $path }}"
                    title="Go to Page {{ $pageNumber }}"
                    class="border border-gray-400 text-gray-700 hover:bg-gray-400 hover:text-gray-700
                    rounded mr-3 px-5 py-3 {{ $pagination->currentPage == $pageNumber ? 'bg-gray-400' : '' }}"
            >{{ $pageNumber }}</a>
        @endforeach

        @if ($next = $pagination->next)
            <a
                    href="{{ $next }}"
                    title="Next Page"
                    class="border border-gray-400 text-gray-700 hover:bg-gray-400 hover:text-gray-700 rounded mr-3 px-5 py-3"
            >&RightArrow;</a>
        @endif
    </nav>
@endif