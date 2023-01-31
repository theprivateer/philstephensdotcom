<?php

use Illuminate\Support\Str;

return [
    'production' => false,
    'baseUrl' => 'http://localhost:8000',
    'title' => 'Phil Stephens',
    'rssTitle' => 'Phil Stephens - All Articles',
    'description' => "Hi, I'm a cyclist and creative software developer.",
    'siteLanguage' => 'en',
    // 'collections' => [
    //     'posts' => [
    //         'author' => 'Phil Stephens',
    //         'sort' => '-date',
    //         'path' => function ($page) {
    //             $slug = Str::slug($page->getFilename());

    //             if (substr($slug, 0, 2) == '20' && substr($slug, 4, 1) == '-') {
    //                 return substr($slug, 11);
    //             }

    //             return $slug;
    //         },
    //         'filter' => function ($item) {
    //             return $item->date;
    //         },
    //     ],
    // ],
    // helpers
    'getDate' => function ($page) {
        return Datetime::createFromFormat('U', $page->date);
    },
    'getExcerpt' => function ($page, $length = 255) {
        if ($page->excerpt) {
            return $page->excerpt;
        }

        if (method_exists($page, 'getContent')) {
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
        }

        if ($page->description) {
            return $page->description;
        }
    },
];
