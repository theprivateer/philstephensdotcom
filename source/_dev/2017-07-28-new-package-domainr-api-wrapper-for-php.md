---
extends: _layouts.dev
section: content
date: 2017-07-28
title: "New Package: Domainr API wrapper for PHP"
---
# New Package: Domainr API wrapper for PHP

Following on from my [little side-project](https://github.com/theprivateer/domain-dictionary) from [a couple of days ago](/building-a-dictionary-of-domain-names) I wanted to be able to dynamically check the availability of a given domain.  Originally I planned to use the AWS Route53 API as I was already familiar with it, but after a couple of tests with it I found that I would very quickly have my API connection throttled as I approached some very meager rate limits - not ideal if I planned to get the status of domains in batches in scheduled jobs.

After a little research I came across a service called [Domainr](https://domainr.com/).  Their [Developer API](https://domainr.build/) is clean and simple and exactly what I was looking for.  After a number of dry runs using Guzzle to make API calls I decided to quickly put together a package to wrap Domainr API with some simple syntax, and add some sugar to some of the responses.  It still needs a test suite written for it, but is available on [Github](https://github.com/theprivateer/domainr) and [Packagist](https://packagist.org/packages/theprivateer/domainr). 

## Installation

The package can be installed using Composer:

```bash
composer require theprivateer/domainr
```

## Usage

For detailed documentation please visit the [Domainr API on Mashape](https://market.mashape.com/domainr/domainr) or Domainr API on [Domainr.build](https://domainr.build/v2.0/docs).  You will need to [subscribe to the Domainr API](https://market.mashape.com/domainr/domainr/pricing) to get a Mashape API key - there is currently a free plan that allows for 10,000 requests per month, however you will need to enter credit card details to cover any overage.

```php
// autoload
include 'path/to/autoload.php'

$client = new \Privateer\Domainr\Domainr('YOUR_MASHAPE_API_KEY');

```

### Search

```php
$client->search($query, $location = null, $registrar = null, $defaults = null);
```
The search method allows you to search for domains by keyword, and receive multiple alternatives back from Domainr.

```php
$client->search('acme.coffee');
```
JSON data will be returned:

```json
[
   {
      "domain":"acme.coffee",
      "host":"",
      "subdomain":"acme.",
      "zone":"coffee",
      "path":"",
      "registerURL":"https:\/\/api.domainr.com\/v2\/register?client_id=mashape-salimgrsy&domain=acme.coffee&registrar=&source="
   },
   {
      "domain":"acme.cafe",
      "host":"",
      "subdomain":"acme.",
      "zone":"cafe",
      "path":"",
      "registerURL":"https:\/\/api.domainr.com\/v2\/register?client_id=mashape-salimgrsy&domain=acme.cafe&registrar=&source="
   },
   {
      "domain":"acme.com.tr",
      "host":"",
      "subdomain":"acme.",
      "zone":"com.tr",
      "path":"",
      "registerURL":"https:\/\/api.domainr.com\/v2\/register?client_id=mashape-salimgrsy&domain=acme.com.tr&registrar=&source="
   }
]
```

### Register

```php
$client->register($domain, $registrar = null);
```
This method returns a string, the value of which is the URL to the domain's registrar:

```php
$client->register('acme.coffee');

// https://domains.google.com/registrar?s=acme.coffee&utm_campaign=domainr.com&utm_content=&af=domainr.com
```

### Status

```php
$client->status($domain);
```
The status method allows you to check domain availability:

```php
$status = $client->status('acme.coffee');
```

This will return an instance of `\Privateer\Domainr\Status`.  The values of the underlying JSON response will be accessible via the `get()` method on the `Status` object:

```php
$status = $client->status('acme.coffee');

$status->get('domain');
// acme.coffee

$status->get('zone');
// coffee

$status->get('status');
// undelegated inactive

$status->get('summary');
// inactive
```

The `Status` object has a number of utility helpers to further explain the response.

```php
$status->get('description');
// Available for new registration.

$status->get('available');
// true
```

These dynamic properties are derived from the [status descriptions](https://domainr.build/docs/status#section-domain-status) in the Domainr API documentation].

The `Status` object also static method to access these values:

```php
Status::description($summary);
```

```php
\Privateer\Domainr\Status::description('inactive'); 
// Available for new registration.

\Privateer\Domainr\Status::available('inactive'); 
// true
```
## Using with Laravel

I did start to put together a Laravel wrapper with a service provider and facade, but decided not to use it in my application.  It can be found on [Github](https://github.com/theprivateer/laravel-domainr) and installed using:

```bash
composer require theprivateer/laravel-domainr
```
