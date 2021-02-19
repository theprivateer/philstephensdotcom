<nav class="hidden lg:flex items-center justify-end text-lg">
    <a title="{{ $page->siteName }} Stream" href="/stream"
       class="ml-6 text-gray-700 font-normal hover:text-orange-600 {{ $page->isActive('/stream') ? 'active text-orange-600' : '' }}">
        Stream
    </a>

    <a title="{{ $page->siteName }} Blog" href="/blog"
        class="ml-6 text-gray-700 font-normal hover:text-orange-600 {{ $page->isActive('/blog') ? 'active text-orange-600' : '' }}">
        Blog
    </a>

    <a title="{{ $page->siteName }} Dev" href="/dev"
       class="ml-6 text-gray-700  font-normal hover:text-orange-600 {{ $page->isActive('/dev') ? 'active text-orange-600' : '' }}">
        Dev
    </a>

    <a title="{{ $page->siteName }} Archive" href="/archive"
       class="ml-6 text-gray-700  font-normal hover:text-orange-600 {{ $page->isActive('/archive') ? 'active text-orange-600' : '' }}">
        Archive
    </a>

    <a title="{{ $page->siteName }} Contact" href="/contact"
        class="ml-6 text-gray-700  font-normal hover:text-orange-600 {{ $page->isActive('/contact') ? 'active text-orange-600' : '' }}">
        Contact
    </a>
</nav>
