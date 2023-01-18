---
extends: _layouts.post
section: content
date: 2021-01-17
title: Adding Reading Time to a Jigsaw Blog
categories: [development]
---
# Adding Reading Time to a Jigsaw Blog

I recently added a simple 'estimated reading time' indicator to the posts on my blog - a simple enough task in PHP, and just as easy with static websites built with [Jigsaw](https://jigsaw.tighten.co) (like this one).

Calculating estimated reading time is a really simple algorithm:

1. Find your total word count.
2. Divide your total word count by an estimated Words Per Minute quantity e.g. [260 for non-fiction](https://www.researchgate.net/publication/332380784_How_many_words_do_we_read_per_minute_A_review_and_meta-analysis_of_reading_rate).
3. The integer value is the number of minutes - if you wish to continue to calculate the number of seconds additional to this, multiply the decimal value (i.e. the value _after_ the decimal point) by 0.60.  However I feel it is sufficient to limit the calculation to minutes, so at this point I would round the number up to the nearest integer.

A simply function based on the above would be:

```php
private function getEstimatedReadingTime($post, $wpm = 260)
{
    $wordCount = str_word_count(strip_tags($post));

    $minutes = (int) ceil($wordCount / $wpm);

    return $minutes . ' min read';
}

```

This function is part of the _listener_ that we are going to create next:

```php
<?php
// /listeners/GenerateEstimatedReadingTime.php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;

class GenerateEstimatedReadingTime
{
    public function handle(Jigsaw $jigsaw)
    {
        $jigsaw->getCollection('posts')->map(function ($post) {
            $post->estimated_reading_time = $this->getEstimatedReadingTime($post);
        });
    }


    private function getEstimatedReadingTime($post, $wpm = 260)
	{
		...
	}
}
```

Here we get the posts collection, iterate through all the items in that collection, and add a new property called `estimated_reading_time` which will contain the proper value. Once our listener has been created we need to hook it to an event. In Jigsaw, every time we build our site, several [events](https://jigsaw.tighten.co/docs/event-listeners/) are fired, those events are:

* beforeBuild
* afterCollection
* afterBuild

The one we are interested on is the **afterCollection** event. This event is fired **after** all collections have been processed but before any output files are written to disk. so let's go ahead and register our listener. We do this in the `bootstrap.php` file as mentioned before.

```php
// /bootstrap.php

$events->afterCollections(App\Listeners\GenerateEstimatedReadingTime::class);

```

With all this in place, the only thing missing is the adding this newly created value to our views. Personally I have a view partial for each post preview, but you can add it to anywhere that is in the scope of your individual post.

```php
<span>{{ $post->estimated_reading_time }}</span>
```
