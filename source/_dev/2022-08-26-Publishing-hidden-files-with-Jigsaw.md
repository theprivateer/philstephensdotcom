---
extends: _layouts.dev
section: content
date: 2022-08-26
title: Publishing hidden files with Jigsaw
---
# Publishing hidden files with Jigsaw

I recently decided to update the URL structure for my blog posts but didn't want to break any of the internal links or search engine indexing that was in place. Luckily this is easy enough with Netlify by utilising static routing [redirects and rewrites](https://docs.netlify.com/routing/redirects/).

I had quite a few redirects so felt the [_redirects file](https://docs.netlify.com/routing/redirects/#syntax-for-the-redirects-file) was the most elegant option with the least complex syntax.

Unfortunately when Jigsaw publishes a site it ignores any source file or directory that is prefixed with an underscore. Unlike other static site generators there is no simple way to whitelist files, so we'll use the next best thing - the `afterBuild` event.

Jigsaw allows you to hook into various stages in the build process to easily inject your own logic, so we can create a simple class to manually copy across our whitelisted files once Jigsaw has finished its own build steps.

First add the event listener to the `./listeners` directory in your Jigsaw project root.

```php
// ./listeners/PublishHiddenFiles.php

<?php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;

class PublishHiddenFiles
{
    protected $files = [
        './_redirects',
        // add any other files you want to copy here...
    ];

    public function handle(Jigsaw $jigsaw)
    {
        foreach($this->files as $file)
        {
            if(file_exists(__DIR__ . '/../source/' . $file))
            {
                file_put_contents(
                    $jigsaw->getDestinationPath() . '/' . $file,
                    file_get_contents(__DIR__ . '/../source/' . $file)
                );
            }
        }
    }
}
```

Next, just register the listener in `./bootstrap.php`

```php
$events->afterBuild(App\Listeners\PublishHiddenFiles::class);
```

If you are using Jigsaw's default [event listener to build a sitemap](https://jigsaw.tighten.com/docs/event-listeners/#registering-event-listeners-as-classes) you will want to put the hidden files event listener _after_ the sitemap builder - this way your hidden files won't appear in the sitemap.
