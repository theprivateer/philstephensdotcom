---
extends: _layouts.dev
section: content
date: 2017-07-18
title: Creating an embedded hashtag system in Laravel - Part 1
---
# Creating an embedded hashtag system in Laravel - Part 1

One of the neat little features of my homegrown microblogging platform is that is has a nifty hashtag autocomplete system like you can find on Twitter and Instagram - you hit the `#` key and as you continue typing, suggestions of existing tags appear in a dropdown menu.

![](/assets/img/autocomplete1.gif)

It was surprisingly quick and easy to implement using a number of existing packages and services, so I thought I would show you how I did it.

## Creating a tagging system

Before I look at the UI I need to have a tagging system in place in my application.  Rather than reinvent the wheel, I'm going to use the [Laravel Tags](https://docs.spatie.be/laravel-tags/v1/introduction) package from the ever-awesome [Spatie](https://spatie.be/en).

I won't go through every aspect of the package - you can refer to the excellent documentation for that - just what we need to get our tagging system up and running.

**Please note: The Laravel Tags package requires Laravel 5.3 or higher, PHP 7.0 or higher and a database that supports `json` fields such as MySQL 5.7.8 or higher.**

You can install the package via composer:

```bash
composer require spatie/laravel-tags
```

Next up, the service provider must be registered in `config/app.php`:

```php
// config/app.php

'providers' => [
    ...
    Spatie\Tags\TagsServiceProvider::class,

];
```

You can publish the migration with:

```bash
php artisan vendor:publish --provider="Spatie\Tags\TagsServiceProvider" --tag="migrations"
```

After the migration has been published you can create the tags and taggables tables by running the migrations:

```bash
php artisan migrate
```

Now we are ready to add tagging capabilities to our posts.  First we create a model for our posts:

```bash
php artisan make:model Post -m
```

We'll keep our post model super-simple for the purposes of this exercise - just a single column for the post body:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('body')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
```

Now all we need to do is add the `HasTags` trait to the `App\Post`:

```php
// app/Post.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use \Spatie\Tags\HasTags;
}
```

We can now attach tags to our posts using commands like:

```php
//adding a single tag
$post->attachTag('tag 1');

//adding multiple tags
$post->attachTags(['tag 2', 'tag 3']);

//using an instance of \Spatie\Tags\Tag
$post->attach(\Spatie\Tags\Tag::createOrFind('tag4'));
```

This is great if we have a separate form field for adding tags to our posts, but we want to add our tags inline, using the `#` character to indicate the tag.  Ideally we also want any hashtags in our posts to be converted to hyperlinks that take us to a list of all posts using a particular tag - so lets deal with both of those requirements in one fell swoop.

## Extracting the tags

Since we are adding HTML markup (the hashtag hyperlink) to plain text we might as well go all out and properly markup all of the text with linebreaks etc.  To achieve this we will use a Markdown parser to cleanly render the content - the added bonus is that our content field will then support all other kinds of Markdown syntax.

My parser of choice is [CommonMark](http://commonmark.thephpleague.com/) from [The League of Extraordinary Packages](http://thephpleague.com/).  Not only is it simple to use, but it has a good architecture for adding custom renderers, which we will leverage for our hashtag extractor.

Add CommonMark to your Laravel project:

```bash
composer require league/commonmark
```

For performance, we will extract the tags when we save a post to the database.  Since we are parsing the Markdown at this point, we may as well store the HTML output it in the database.  We could _just_ save the rendered HTML instead of the original Markdown, but I prefer to store it in addition to the original content as it allows for easy editing in the future.

We will need to add another column to the `posts` table (either by editing your original migration, or by creating an additional migration):

```php
...
$table->text('html')->nullable()->default(null);
...
```

Since we want to render the content and extract any tags whenever the post is saved - both when it is created and edited - we will use model events to trigger the rendering.

```php
// app/Post.php

namespace App;

use Illuminate\Database\Eloquent\Model;
use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use Spatie\Tags\HasTags;

class Post extends Model
{
    use HasTags;
    
    public static function boot()
    {
        parent::boot();

        self::saving( function($model) {
        
            $environment = Environment::createCommonMarkEnvironment();
            $parser = new DocParser($environment);
            $htmlRenderer = new HtmlRenderer($environment);

            $text = $parser->parse($model->body);

            $model->html = $htmlRenderer->renderBlock($text);
        });
    }
}

```

Next we need to create a custom inline parser for CommonMark - luckily this is easy to do by repurposing one of the [examples in the documentation](http://commonmark.thephpleague.com/customization/inline-parsing/#examples) that extracts and converts Twitter-like handles into hyperlinks:

```php
// app/Parsers/HashtagParser.php

namespace App\Parsers;

use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\InlineParserContext;

class HashtagParser extends AbstractInlineParser
{
    public function getCharacters()
    {
        return ['#'];
    }

    public function parse(InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();

        // The # symbol must not have any other characters immediately prior
        $previousChar = $cursor->peek(-1);

        if ($previousChar !== null && $previousChar !== ' ') {
            // peek() doesn't modify the cursor, so no need to restore state first
            return false;
        }

        // Save the cursor state in case we need to rewind and bail
        $previousState = $cursor->saveState();

        // Advance past the # symbol to keep parsing simpler
        $cursor->advance();

        // Parse the tag
        $tag = $cursor->match('/^[A-Za-z0-9_]{1,100}(?!\w)/');

        if (empty($tag)) {
            // Regex failed to match; this isn't a valid Twitter handle
            $cursor->restoreState($previousState);
            return false;
        }

        $tagUrl = '/tag/' . $tag;

        $inlineContext->getContainer()->appendChild(new Link($tagUrl, '#' . $tag));

        return true;
    }
}

```

For the sake of this project we're going to assume that the URL to access posts with a particular tag is `/tag/[THE TAG]`, but feel free to update for your own purposes.

Add the new parser to your boot method in `App\Post`:

```php
// app/Post.php

...

public static function boot()
{
    parent::boot();

    self::saving( function($model) {
		...            
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser(new \App\Parsers\HashtagParser());
        $parser = new DocParser($environment);
        $htmlRenderer = new HtmlRenderer($environment);
        ...
    });
}

...
```

So far our application is rendering out all hashtags as hyperlinks, but we're not actually attaching the tags to the posts yet.  Since our Markdown parser is running _before_ the model is persisted to the database, we need to pass the tags that it has found to a container to be processed _after_ the post is saved.  We'll create a very simple class to handle this for us:

```php
// app/TagQueue.php

namespace App;


class TagQueue
{
    private $tags = [];

    public function addTag($tag)
    {
        $this->tags[] = $tag;
    }

    public function getTags()
    {
        return $this->tags;
    }
}
```

Now we can instatiate this in our post model:

```php
// app/Post.php

...
public static function boot()
{
    parent::boot();

    self::saving( function($model) {
		...            
		// Set up a container for any hashtags that get parsed
        App::singleton('tagqueue', function() {
            return new \App\TagQueue;
        });

        $environment = Environment::createCommonMarkEnvironment();
        ...
    });
}
...
```

And then pass the tags we find to it in the hashtag parser:

```php
// app/Parsers/HashtagParser.php

...
if (empty($tag)) {
    // Regex failed to match; this isn't a valid Twitter handle
    $cursor->restoreState($previousState);
	return false;
}

// Need to dispatch here to attach the tag (or queue for tagging) to the post
app('tagqueue')->addTag($tag);

$tagUrl = '/tag/' . $tag;
...
```

Finally, we can access any tags that we have detected after the post has been saved and attach them to the post:

```php
// app/Post.php

...
public static function boot()
{
    parent::boot();

    self::saving( function($model) {
		...            
    });
    
    self::saved( function($model) {
        $model->syncTags(app('tagqueue')->getTags());
	});
}
...
```

Now that we have the embedded hashtag system working on the backend we can work on building the frontend implementation. In my next post we'll look at building out the autocomplete/suggestion UI.

**Part 2 of this tutorial can be found at [Creating an embedded hashtag system in Laravel - Part 2](/blog/creating-an-embedded-hashtag-system-in-laravel-part-2).** 
