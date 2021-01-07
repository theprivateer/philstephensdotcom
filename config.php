<?php

use Illuminate\Support\Str;

return [
    'baseUrl' => 'http://philstephens.test',
    'production' => false,
    'siteName' => 'Phil Stephens',
    'siteDescription' => 'Personal website of Phil Stephens',
    'siteAuthor' => 'Phil Stephens',

    // collections
    'collections' => [
        'posts' => [
            'author' => 'Phil Stephens', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'blog/{_filename}',
        ],
        'dev' => [
            'author' => 'Phil Stephens', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'dev/{_filename}',
        ],
        'archive' => [
            'author' => 'Phil Stephens', // Default author, if not provided in a post
            'sort' => '-date',
            'path' => 'archive/{_filename}',
        ],
        'categories' => [
            'path' => '/blog/categories/{_filename}',
            'posts' => function ($page, $allPosts) {
                return $allPosts->filter(function ($post) use ($page) {
                    return $post->categories ? in_array($page->getFilename(), $post->categories, true) : false;
                });
            },
        ],
    ],

    // helpers
    'getDate' => function ($page) {
        return Datetime::createFromFormat('U', $page->date);
    },
    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        $content = preg_split('/<!-- more -->/m', $page->getContent(), 2);
        $cleaned = trim(
            strip_tags(
                preg_replace(['/<pre>[\w\W]*?<\/pre>/', '/<h\d>[\w\W]*?<\/h\d>/'], '', $content[0]),
                '<code>'
            )
        );

        if (count($content) > 1) {
            return $cleaned;
        }

        $truncated = substr($cleaned, 0, $length);

        if (substr_count($truncated, '<code>') > substr_count($truncated, '</code>')) {
            $truncated .= '</code>';
        }

        return strlen($cleaned) > $length
            ? preg_replace('/\s+?(\S+)?$/', '', $truncated) . '...'
            : $cleaned;
    },
    'isActive' => function ($page, $path) {

        if(Str::startsWith(trimPath($page->getPath()),  trimPath($path))) return true;

        return Str::endsWith(trimPath($page->getPath()), trimPath($path));
    },
];
