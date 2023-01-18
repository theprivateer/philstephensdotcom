---
extends: _layouts.post
section: content
date: 2017-07-19
title: "New Package: Clickable URLs and Email Addresses in PHP"
categories: [development]
---
# New Package: Clickable URLs and Email Addresses in PHP

Last night I refactored a few functions I had put together into a package to convert all URLs, email addresses and FTP addresses in a block of text into clickable HTML anchors.  I use it on my [microblog](https://shortform.philstephens.io) when posting - since my posts are [automatically reposted to Twitter](/blog/cross-posting-to-twitter-using-laravel-notifications), which marks up links accordingly, I want to avoid using Markdown for clickable URLs etc.

The package can be installed using Composer:

```bash
composer require theprivateer/clickable
```

To use it, it's just a matter of passing a string to the static `parse` method on the class:

```php
require_once 'path/to/composer/autoload.php';

echo \Privateer\Clickable\Clickable::parse('Read my blog at https://philstephens.com');

// Read my blog at <a href="https://philstephens.com" rel="nofollow">https://philstephens.com</a>
```

Alternatively you can use the `str_clickable` helper function (also autoloaded by Composer):

```php
require_once 'path/to/composer/autoload.php';

$str = 'Find out more at https://packagist.org/packages/theprivateer/clickable.';

echo str_clickable($str)

// Find out more at <a href="https://packagist.org/packages/theprivateer/clickable" rel="nofollow">https://packagist.org/packages/theprivateer/clickable</a>.
```

If you pass through a string that already has an anchor tag in it, fear not - the parser will automatically ignore them:

```php
require_once 'path/to/composer/autoload.php';

$str = 'Find out more at <a href="https://packagist.org/packages/theprivateer/clickable" rel="nofollow">https://packagist.org/packages/theprivateer/clickable</a>.';

echo str_clickable($str)

// Find out more at <a href="https://packagist.org/packages/theprivateer/clickable" rel="nofollow">https://packagist.org/packages/theprivateer/clickable</a>.
```

I've still got some documenting to do and a simple test suite to write, but if you want to find out more check it out [on Github](https://github.com/theprivateer/clickable).
