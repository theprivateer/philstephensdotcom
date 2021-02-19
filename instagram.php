<?php

include __DIR__ . '/vendor/autoload.php';

$payload = json_decode(file_get_contents(__DIR__ . '/instagram/media.json'));

$posts = collect($payload->photos)->merge($payload->videos);

$posts = $posts->sortBy('taken_at')->reverse();


$i = $posts->count();

$last_timestamp = null;
$post = [];

foreach($posts as $photo)
{
    $parts = explode('/', $photo->path);

    $folder = $parts[1];

    $path = $photo->path;

    if ( ! file_exists(__DIR__ . '/instagram/' . $path)) continue;

    $target = str_replace(['photos/', 'videos/'], '/assets/img/stream/', $path);

    $parts = explode('/', $photo->path);

    // Directory exist?
    if( ! is_dir(__DIR__ . '/source/assets/img/stream/' . $folder))
    {
        // Make directory
        mkdir(__DIR__ . '/source/assets/img/stream/' . $folder);
    }

    // copy the file
    copy(__DIR__ . '/instagram/' . $path, __DIR__ . '/source' . $target);

    if(empty($post) || $photo->taken_at != $post['taken_at'])
    {
        // Write the old file
        if( ! empty($post))
        {
            write_post_file($post);

        }

        // Start a new file
        $post = [];
        $post['taken_at'] = $photo->taken_at;
        $post['id'] = $i;
        $post['images'] = [
          $target
        ];

        $post['date'] = \Carbon\Carbon::parse($photo->taken_at)->format('Y-m-d');

        // Autolink hashtags
        $post['caption'] = preg_replace('/(?<!\S)#([0-9a-zA-Z]+)/', '<a href="https://www.instagram.com/explore/tags/$1">&#35;$1</a>', $photo->caption);

        // Autolink mentions
        $post['caption'] = preg_replace('/(?<!\S)@([0-9a-zA-Z_]+)/', '<a href="https://www.instagram.com/$1">@$1</a>', $post['caption']);


        // Insert line returns
        $post['caption'] = str_replace(["\n.\n", " .\n", "\n. "], "\n\n", $post['caption']);
    } else {


        $post['images'][] = $target;
    }

    $i--;
}

write_post_file($post);

function write_post_file($post)
{
    if( ! empty($post))
    {
        // Write the file
        $content = "---
extends: _layouts.stream
section: content
images: " . json_encode($post['images']) . "
date: {$post['date']}
---

{$post['caption']}";

        file_put_contents(__DIR__ . '/source/_stream/' . $post['id'] . '.md', $content);
    }
}