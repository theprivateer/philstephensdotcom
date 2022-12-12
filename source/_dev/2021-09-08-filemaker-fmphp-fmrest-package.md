---
extends: _layouts.dev
section: content
date: 2021-09-08
title: "New Package: FileMaker PHP and FMREST library"
---
# New Package: FileMaker PHP and FMREST library

In my day job at [Databee](https://databee.com.au) my PHP development projects tend to leverage [Claris FileMaker](https://www.claris.com/filemaker/) as the database engine.  Without going into too much detail, FileMaker varies quite differently from most database engines, and as such the method to connect to it is somewhat unique.

Up until a couple of years ago, the only way to connect was by using an XML interface with a convenient PHP wrapper (provided by FileMaker).  Whilst perfectly serviceable this method came with some caveats and idiosyncrasies that meant making connections performant - especially for large datasets - a little more complicated.

Then Claris released a RESTful API for retrieving data in JSON format - much more performant, but with the caveat that connections to the database were metered (FileMaker is a licensed product, with data allowances incorporated into an annual fee).

Each connection method already have open-source packages that you can use to streamline development, but switching from one to the other requires significant retooling of your code. Consider the two snippets below. They both connect to a FileMaker database and retrieve a record from a `users` layout where the email address is `phils@hey.com`.  It then echoes out the `name` field of that record. It's just about the simplest query you can do, but let's see what is involved...

Using the PHP API:

```php
// Using the airmoi/FileMaker package
use airmoi\FileMaker\FileMaker;
use airmoi\FileMaker\FileMakerException;

$host = '127.0.0.1';
$database = 'DatabaseName';
$user = 'admin';
$password = 'someP@ssword';

$fm = new FileMaker($database, $host, $user, $password);

try
{
    $command = $fm->newFindCommand('users')
                    ->addFindCriterion('email', '"phils@hey.com"');
    $record = $command->execute()->getFirstRecord();
} catch (FileMakerException $e)
{
   // this exception can be thrown if no records are found
   echo 'An error occured ' . $e->getMessage() . ' - Code : ' . $e->getCode();
}

echo $result->getField('name');
// Phil Stephens
```

Using the Data API (forgoing exception handling for a little more brevity):

```php
use GuzzleHttp\Client;

$host = '127.0.0.1';
$database = 'DatabaseName';
$user = 'admin';
$password = 'someP@ssword';

$uri = 'https://' . $host . '/fmi/data/v2/databases/' . $database . '/';

$client = new Client(['base_uri' => $uri]);

// Authenticate and generate a session token
$response = $client->request('POST', 'sessions',
					[
						'auth' => [$user, $password],
						'headers' => [
							'Content-Type' => 'application/json'
						]
					]);

$body = json_decode($response->getBody());
$token = $body->response->token;

// Make the actual query
$response = $client->request('POST', 'layouts/users/_find', 
					[
						'headers' => [
							'Authorization' => 'Bearer ' . $token,
							'Content-Type' => 'application/json'
						],
						'body' => {
						  "query":[
							    {"email": "==phils@hey.com"}
						    ]
						}
					]);	

$body = json_decode($response->getBody());

echo $body->response->data[0]->fieldData['name'];
// Phil Stephens
```

There has to be a better, more object-orientated way, right?

Heavily inspired by Laravel's [database query builder](https://laravel.com/docs/8.x/queries) I decided to whip up a new package that would allow me to abstract away all of the verbose connection logic and hide it behind a more elegant object-orientated syntax. I also wanted the connection method to be interchangeable between traditional PHP-API and FMREST.

## The Package

You can install the package via Composer:

```bash
composer require theprivateer/filemaker
```

### Basic usage

Following on from the previous examples to achieve the same result the code would be as follows:

```php
$config = [
    'driver' => 'fmphp',
    'host' => '127.0.0.1',
    'file' => 'DatabaseName',
    'user' => 'admin',
    'password' => 'someP@ssword',
];

$fm = new Privateer\FileMaker\FileMaker($config);

$user = $fm->layout('users')->where('email', 'phils@hey.com')->first();

echo $user->name;
// Phil Stephens
```

Much cleaner, right? In order to use the FMREST Data API, all that you would need to change is the `driver` value in `$config` to `fmrest`. And that's it.

[comment]: <> (Unlike some of the other packages I have put together that tend to offer some rudimentary time-saving functionality, this package has some 20+ &#40;and growing&#41; distinct commands available that all deserve to be properly documented.  Inspired by the [docs that Spatie maintain]&#40;https://spatie.be/docs&#41; for their larger packages, I have decided to start adding more in depth documentation to this site.  First up will be this FileMaker package - watch this space!)
