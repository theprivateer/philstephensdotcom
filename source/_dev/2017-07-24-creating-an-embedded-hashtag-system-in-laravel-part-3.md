---
extends: _layouts.dev
section: content
date: 2017-07-24
title: Creating an embedded hashtag system in Laravel - Part 3
---
# Creating an embedded hashtag system in Laravel - Part 3

In my previous post I outlined how to integrate [Algolia search](https://www.algolia.com) into our front-end hashtag autocomplete, but I thought I would give a quick alternative to using an external service.  This example largely follows the same structure as a a couple of [recent](https://laracasts.com/series/lets-build-a-forum-with-laravel/episodes/60) [screencasts](https://laracasts.com/series/lets-build-a-forum-with-laravel/episodes/61) over on [Laracasts](https://laracasts.com/), where a username @mention autocomplete is implemented, so if you've seen those this will all be familiar.

## The Front-end

The tutorial on Laracasts goes through how to install the necessary dependencies using `npm`, so we'll keep things really simple here and just download the packages directly from Github.

We will leveraging [At.js](https://github.com/ichord/At.js/) to power the frontend functionality.  Download the contents of the `dist` directory and copy to somewhere within your apps `public` directory.

At.js also has [Caret.js](https://github.com/ichord/Caret.js) as a dependency - go ahead and copy the contents of that project's `dist` directory to your `public` directory.

As we did with the Algolia example we can now build out a simple HTML view to demonstrate everything working:

```html
<head>
	<!-- Basic Bootstrap styling -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	
	<!-- At.js styles -->
	<link rel="stylesheet" href="/path/to/jquery.atwho.css">
	
</head>

<body>
	<textarea class="form-control" rows="5" id="autocomplete-textarea" name="body">
	
	<!-- jQuery -->
	<script src="http://code.jquery.com/jquery.js"></script>

	<!-- Caret.js -->
	<script src="/path/to/jquery.caret.js"></script>

	<!-- At.js -->
	<script src="/path/to/jquery.atwho.js"></script>
</body>
```

The Javascript for this example is considerably shorter than the Algolia method:

```javascript
$(function() {
    $('#autocomplete-textarea').atwho({
        at: '#',
        // Adjust the delay in milliseconds to throttle requests to the server
        delay: 500,
        callbacks: {
            remoteFilter: function(query, callback) {
                $.getJSON('/api/tags', {tag: query}, function(tags) {
                    callback(tags);
                });
            }
        }
    })
});
```

Here we are using a remote filter to retrieve the list of potential tags based on what we type.

## The Back-end

We've told At.js to refer to a server-side route to get a list of potential hashtags based on what we have already typed.  The first step to making this all come together is to create a new controller to handle this request:

```bash
php artisan make:controller Api/TagsController
```

This controller will have a single method to search for tags based on what is being typed on the front-end:

```php
// app/Http/Controllers/Api/TagsController
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TagsController extends Controller
{
    public function index()
    {
        $search = request('tag');

        $locale = $locale ?? app()->getLocale();

        return \Spatie\Tags\Tag::query()
            ->where("name->{$locale}", 'LIKE', "%$search%")
            ->take(5)
            ->pluck("name");
    }
}

```

Since we are not using Algolia in this example, we can reference the original `Tag` model from the Spatie package.  Since the package provides multi-lingual support out of the box, we need to determine what the app's locale is so that we can reference to the correct value in MySQL's JSON column.

This code will return up to five results, but feel free to adjust that number to meet your needs.

The final step is to wire it all up with the route that we specified in the front-end Javascript:

```php
// routes/web.php
...
Route::get('api/tags', 'Api\TagsController@index');
...
```

## The End Result

Ultimately the end user experience is largely the same as the Algolia method.  We don't benefit from typo-tolerance out of the box, but it's up to you to decide if you really need it.  This implementation is entirely self-contained, and with careful monitoring and tuning should scale well without any performance hits.

![](/assets/img/autocomplete_atjs.gif)
