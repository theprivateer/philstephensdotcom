<nav id="js-nav-menu" class="nav-menu hidden lg:hidden">
    <ul class="my-0">
        <li class="pl-4">
            <a
                    title="{{ $page->siteName }} Stream"
                    href="/stream"
                    class="nav-menu__item font-normal hover:text-orange-500 {{ $page->isActive('/stream') ? 'active text-orange' : '' }}"
            >Stream</a>
        </li>

        <li class="pl-4">
            <a
                title="{{ $page->siteName }} Blog"
                href="/blog"
                class="nav-menu__item font-normal hover:text-orange-500 {{ $page->isActive('/blog') ? 'active text-orange' : '' }}"
            >Blog</a>
        </li>

        <li class="pl-4">
            <a
                    title="{{ $page->siteName }} Blog"
                    href="/dev"
                    class="nav-menu__item font-normal hover:text-orange-500 {{ $page->isActive('/dev') ? 'active text-orange' : '' }}"
            >Dev</a>
        </li>

        <li class="pl-4">
            <a
                    title="{{ $page->siteName }} Blog"
                    href="/archive"
                    class="nav-menu__item font-normal hover:text-orange-500 {{ $page->isActive('/archive') ? 'active text-orange' : '' }}"
            >Archive</a>
        </li>

        <li class="pl-4">
            <a
                title="{{ $page->siteName }} Contact"
                href="/contact"
                class="nav-menu__item font-normal hover:text-orange-500 {{ $page->isActive('/contact') ? 'active text-orange' : '' }}"
            >Contact</a>
        </li>
    </ul>
</nav>
