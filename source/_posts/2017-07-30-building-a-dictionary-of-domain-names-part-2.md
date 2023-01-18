---
extends: _layouts.post
section: content
date: 2017-07-30
title: Building a Dictionary of Domain Names - Part 2
categories: [development]
---
# Building a Dictionary of Domain Names - Part 2

[Previously I discussed a little hobby project](/building-a-dictionary-of-domain-names) that I was working on to build out a dictionary of words that could be made into complete domain names (i.e. the whole domain, including the TLD extension, forms the word).

I went through the process of importing a public domain English dictionary, scraping a list of available TLD extensions from my registrar of choice (Amazon Web Services) and then building out a list of possible domains.

I was looking at was to check domain availability via the AWS Route53 API, but discovered the throttling and rate limits made it prohibitively slow for batch processing.  On discovering the [Domainr API](https://domainr.build/), I put together [a PHP wrapper](/blog/new-package-domainr-api-wrapper-for-php) to make things a little more fluent - now it's time to build that into my dictionary app.

## Checking Domain Availability

First of all we need to pull in my Domainr package using Composer:

```bash
composer require theprivateer/domainr
```

In order to use the API we will need a Mashape API key - you can [subscribe to the Domainr API for free here](https://market.mashape.com/domainr/domainr/pricing).

Next we will add the following configuration to our `config/services.php`:

```php
// config/services.php

...
'domainr'	=> [
		'mashape_api_key' => env('MASHAPE_API_KEY),
	],
...
```
... and then add the corresponding variable to your `.env` file.

We're going to want to persist our API results to the database for faster lookup, so we'll need to add a couple of columns to the `domains` table:

```bash
php artisan make:migration add_status_columns_to_domains --table=domains
```
We're going to be recording three pieces of information from our lookups - the domain status, the timestamp of the check and whether or not the domain is actually available (based on the status values in the [Domainr documentation](https://domainr.build/docs/status#section-domain-status))

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusColumnsToDomains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->boolean('available')->default(false);
            $table->string('status')->nullable()->default(null);
            $table->dateTime('last_checked_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['available', 'status', 'last_checked_at']);
        });
    }
}
```

At this stage we won't be checking domain status on the fly, so we'll encapsulate the logic for querying the Domainr API in a queueable job:

```bash
php artisan make:job CheckDomainStatus
```
The job will take a single instance of a `Domain`, perform the Domainr API call on that domain, and then persist the results to the database:

```php
// app/Jobs/CheckDomainStatus.php

namespace App\Jobs;

use App\Domain;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Privateer\Domainr\Domainr;

class CheckDomainStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var Domain
     */
    private $domain;

    /**
     * Create a new job instance.
     *
     * @param Domain $domain
     */
    public function __construct(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $status = (new Domainr(config('services.domainr.mashape_api_key')))->status($this->domain->domain);

		$this->domain->status = $status->get('summary');
        $this->domain->last_checked_at = new Carbon;

		// uses the availability utility method on the Privateer\Domainr\Status class
        $this->domain->available = $status->get('available');

        $this->domain->save();

        return;
    }
}
```

We'll run this job in batches on a regular schedule, but to make it easier to run manually (for testing and debugging) we will dispatch this job from within an Artisan command, which we can also call from within Laravel's task scheduler.

First create the Artisan command in `routes/console.php`:

```php
// routes/console.php

...
Artisan::command('domain:check', function () {

    $domains = \App\Domain::orderBy('last_checked_at')
    						->take(env('DOMAINR_BATCH_SIZE', 50))
    						->get();

    foreach($domains as $domain)
    {
        dispatch(new \App\Jobs\CheckDomainStatus($domain));
    }

})->describe('Check domain status');
...
```
We want to always check domain availability for those domains that we last checked the longest ago (or never) and we'll define the batch size to check via an optional variable in our `.env` file (`DOMAINR_BATCH_SIZE`);

This command can now be run via the command line with:

```bash
php artisan domain:check
```

Next this can be added to the task scheduler in `app/Console/Kernel.php`:

```php
// app/Console/Kernel.php

...
protected function schedule(Schedule $schedule)
{
    $schedule->command('domain:check')->hourly();
}
...
```
Using my dictionary import and the TLDs I was able to scrape from AWS Route53 I was able to generate 13,659 possible domains.  Using the scheduled job above with the default batch size, each domain's status will be checked once every 11 days or so which, once I have an initial status for each domain (acheived by vastly increasing the batch size and running the Artisan command manually) this is an acceptable delay on getting an updated status.

### Registering Available Domains

Now that we can check domain availability it would be great to be able to easily register ones that take our fancy.  Once again the Domainr API has our back - after a fashion.

The `register` API endpoint returns the URL to the domain registration page at a  relevant registrar (often with a cheeky little discount via the Domainr referral).  For now we'll hook into this directly and just redirect users off-site for registrations.

First we'll need a controller to handle the redirect:

```bash
php artisan make:controller RegisterController
```
We will pass a domain to the controller method using route model binding:

```php
// app/Http/Controllers/RegisterController

namespace App\Http\Controllers;

use App\Domain;
use Illuminate\Http\Request;
use Privateer\Domainr\Domainr;

class RegisterController extends Controller
{
    public function create(Domain $domain)
    {
        $url = (new Domainr(config('services.domainr.mashape_api_key')))->register($domain->domain);

        return redirect($url);
    }
}
```
...finally we can wire it up with a simple route:

```php
// routes/web.php

...
Route::get('register/{domain}', 'RegisterController@create');
...
```

### Updating the UI

The update to the UI is a simple one - in `resources\views\words\index.blade.php` we need to replace the `foreach` loop for the domains with a HTML `table` featuring the extended information that we are now recording, plus a link to the registration route if the domain is available:

```html
<div class="panel panel-default">
    <div class="panel-body">
        <h3>{{ $word->word }}</h3>

        @foreach($word->definitions as $definition)
            <p><em class="text-muted">{{ $definition->type }}</em> {{ $definition->definition }}</p>
        @endforeach

        @if($word->domains()->count())
        <table class="table table-striped">
            <tbody>
            @foreach($word->domains as $domain)
                <tr>
                    <td><a href="http://{{ $domain->domain }}">{{ $domain->domain }}</a></td>
                    <td><span class="label label-{!! ($domain->available) ? 'success">AVAILABLE' : 'danger">UNAVAILABLE' !!}</span></td>
                    <td>{{ \Privateer\Domainr\Status::description($domain->status) }}</td>
                    <td>
                        @if($domain->available)
                            <a href="/register/{{ $domain->id }}" target="_blank" class="btn btn-default btn-sm">Register</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @else
            <div class="alert alert-info text-center">No matching domains</div>
        @endif
    </div>
</div>
```

![](/assets/img/snapstack/1/I4cZ9d2b92inmq2GwbIaoxT4oOaccRPuacxvTlcw.png)


## Moar Domains!

My original reason for getting the TLDs from AWS was so that I could use the Route53 API to check domain availability, but now that I'm using the Domainr API I thought I would take a look at their TLD list.

The Domainr homepage proudly claims to have over 1,700 TLDs, which is quite a jump - so I set about updating my TLD seeder to use the Domainr site.

### Scraping the Domainr TLD list

Domainr has a [handy list](https://domainr.com/about/tlds) of all of the TLDs that it can check so this looked to be an easy amendment to the seeder file.  The only extra step that needed to be introduced was to check the the TLD only contained latin characters (Domainr is able to check a number of internationalised domain names):

```php
// database/seeds/TldSeeder.php

use Goutte\Client;
use Illuminate\Database\Seeder;

class TldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();

        $crawler = $client->request('GET', 'https://domainr.com/about/tlds');

        $crawler->filter('div.domains a')->each(function ($node) {
            $tld = $node->text();

            if(strpos($tld, '.') === 0)
            {
                $parts = explode(' ', $tld);

				// Ensure that the TLD only contains latin characters and periods
                if ( ! preg_match('/[^a-z.]/', $parts[0]))
                {
                    $_tld = \App\Tld::firstOrCreate([
                        'extension'  => $parts[0],
                    ]);
                }
            }
        });
    }
}
```
I ran this individual seeder again and was very happy with the result, until I noticed some notable omissions.  On closer inspection I discovered that this list only contained the apex extensions and none of the commonly used lower-level extensions.  For example, in Australia the de-facto TLD is `.com.au`, whereas the Domainr list only had `.au`.  All of the _subdomains_ were listed on the TLDs individual page, so I needed to introduce a second level of crawling to my seeder:

```php
// database/seeds/TldSeeder.php

use Goutte\Client;
use Illuminate\Database\Seeder;

class TldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();

        $crawler = $client->request('GET', 'https://domainr.com/about/tlds');

        $crawler->filter('div.domains a')->each(function ($node) use ($client) {
            $tld = $node->text();

            if(strpos($tld, '.') === 0)
            {
                $parts = explode(' ', $tld);

				// Ensure that the TLD only contains latin characters and periods
                if ( ! preg_match('/[^a-z.]/', $parts[0]))
                {
                    $_tld = \App\Tld::firstOrCreate([
                        'extension'  => $parts[0],
                    ]);
                }

                // Now crawl the TLDs page
                $subdomain_crawler = $client->request('GET', 'https://domainr.com' . $node->attr('href'));

                $subdomain_crawler->filter('#subdomains a.dim')->each(function ($node) use ($_tld) {

                    $tld = $node->text();

                    if(strpos($tld, '.') === 0)
                    {
                        $parts = explode(' ', $tld);

                        $_subtld = \App\Tld::firstOrCreate([
                                'extension'  => $parts[0]
                            ]);
                    }
                });
            }
        });
    }
}
```
Now we can rerun the TldSeeder again - since we are adding entries using `firstOrCreate()` every run of the seeder is non-destructive to anything that has already been recorded.

```bash
php artisan db:seed --class=TldSeeder
```
The same is true for the domain seeder - every pass will only add new domain possibilities:

```bash
php artisan db:seed --class=DomainSeeder
```
After running these two seeders on the results from the Domainr site my TLD count went from **313** to **4,048**, and my domain permutations went from **13,659** to **85,317**. I _may_ need to revisit my availibility checking strategy!
