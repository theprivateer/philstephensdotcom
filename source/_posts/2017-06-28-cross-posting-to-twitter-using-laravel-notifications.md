---
extends: _layouts.post
section: content
date: 2017-06-28
title: Cross-posting to Twitter using Laravel Notifications
categories: [development]
---
# Cross-posting to Twitter using Laravel Notifications

A couple of days ago I [wrote about the micro-blogging platform](/blog/micro-blogging) that I was developing for my own use, and how in time I would be setting it up to automatically cross-post to my Twitter feed.  Well it turns out it was a whole lot easier than I expected using Laravel's built in [Notifications](https://laravel.com/docs/5.4/notifications) feature (I haven't really talked about the underlying code for Shortform, but it is largely built on the awesome [Laravel PHP framework](https://laravel.com)).

One of the great things about this particular feature is that the community has really embraced it and built a ton of additional notification channel integrations over and above the email, SMS and Slack channels that ship with Laravel - all curated over at [laravel-notification-channels.com](http://laravel-notification-channels.com/).  One of these channels is, of course, [Twitter](http://laravel-notification-channels.com/twitter/) via a package by [Christoph Rumpel](http://christoph-rumpel.com/).

## Setting Up

First, install the package into your Laravel project:

```bash
composer require laravel-notification-channels/twitter
```

Next, add the provider to `config/app.php`

```php
'providers' => [
    ...
     NotificationChannels\Twitter\TwitterServiceProvider::class,
],
```

Finally, [create a new app on Twitter](https://apps.twitter.com) and add your config to the `config/services.php` file and your `.env`:

```php
...
'twitter' => [
    'consumer_key' => env('TWITTER_CONSUMER_KEY'),
    'consumer_secret' => env('TWITTER_CONSUMER_SECRET'),
    'access_token' => env('TWITTER_ACCESS_TOKEN'),
    'access_secret' => env('TWITTER_ACCESS_SECRET')
],
```

**Tip:** you can retrieve the necessary credentials by going to the **Keys and Access Tokens** tab of you Twitter app page.  The consumer key and secret will be at the top of the page, and you will need to generate a new access token and key below that.

## Sending the Tweet

For my setup, I have a Post model that I’ll be using to send out the tweet from, but you can use any model you already have. Just add the Notifiable trait to it:

```php
class Post extends Model
{
    use Notifiable;
```

Next, create your notification class:

```bash
php artisan make:notification PostPublished
```

Open up this file and adjust the via method and add a `toTwitter` method. Here is the completed class:

```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class PostPublished extends Notification
{
    public function via($notifiable)
    {
        return [TwitterChannel::class];
    }

    public function toTwitter($notifiable)
    {
        return new TwitterStatusUpdate('Hey I just posted something');
    }
}
```

Now to call this all you have to do is grab a Post and send it off:

```php
$post = Post::find(1);
$post->notify(new PostPublished());
```

The final change we need to make is to update the `toTwitter` method in the `PostPublished` class, so it includes the actual post data.  Since this is a micro-blogging platform I want to preserve as much of the original post as possible, so I'll follow some simple rules:

1. If the post is 140 characters or less, I'll just send it as-is;
2. If the post is more than 140 characters, I will truncate it and append a link back to the original post

```php
public function toTwitter($notifiable)
{
    if(strlen($notifiable->body) <= 140)
    {
        $post = $notifiable->body;
    } else
    {
        $post = str_limit($notifiable->body, 120) . ' ' . url($notifiable->uri);
    }

    return new TwitterStatusUpdate($post);
}
```

## Adding Media

The `TwitterStatusUpdate` class also has a method for adding images to tweets.  In the case of Shortform, my posts can optionally have a large featured/hero image as the subject (like Instagram), so in those cases I would also like to push the image across to be displayed natively in Twitter:

```php
public function toTwitter($notifiable)
{
    if(strlen($notifiable->body) <= 140)
    {
        $post = $notifiable->body;
    } else
    {
        $post = str_limit($notifiable->body, 120) . ' ' . url($notifiable->uri);
    }

    $tweet = new TwitterStatusUpdate($post);

    if($image = $notifiable->featuredImage)
    {
        return $tweet->withImage([$notifiable->featuredImage]);
    }

    return $tweet;
}
```

And that's all there is to it!  What I originally thought was going to be a more complicated task was solved in a matter of minutes - all I do now is trigger the `PostPublished` notification every time a post is created.

I think next I will quickly add this to this blog (also on a custom-made platform) to push new posts into Twitter.  All I will need to do is use the post title - trimmed if over 120-or-so characters - and append the post URL!
