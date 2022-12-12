---
extends: _layouts.dev
section: content
date: 2017-07-26
title: Building a Dictionary of Domain Names
---
# Building a Dictionary of Domain Names

Yesterday I was thinking about what domain names are available that, including their extension, can create complete words.  For example, instead of `glorious.com` using `glorio.us`.  Obviously there are a _lot_ of words in the English language, and an ever-growing number of top level domain extensions available for registration, so the number of permutations must be massive. Being a developer and tinkerer, rather than searching for a service that no doubt already exists to discover these options I set myself a little challenge to build one myself.

As soon as I clocked-off from work I span up a new Laravel installation and set about figuring out how I would approach this task.

## The Dictionary

It all starts with the words.  Ideally we want to create a dictionary of real words in our own database.  A quick five minute Google search for open-source dictionaries and I came across [The Online Plain Text English Dictionary](http://www.mso.anu.edu.au/~ralph/OPTED/).

Each letter of the alphabet has it's own html document - with very simple and  minimal markup.  Each word and it's definition is within a `<p>` tag, with the word itself and the type (i.e. noun, verb etc) enclosed in `<b>` and `<i>` tags respectively.  This makes it extremely easy to crawl, so we'll use a package called [Goutte](https://github.com/FriendsOfPHP/Goutte) to do all of the heavy lifting for us.

It can be installed using Composer, and requires no additional configuration:

```bash
composer require fabpot/goutte
```

### The Word model

As already mentioned, the words that we will be extracting have three identifiable components:

* The word itself
* The type (i.e. noun, verb etc)
* The definition

On my first run I had all three elements within a single Word eloquent model, but quickly discovered a lot of duplication as many words have multiple definitions, and the OPTED pages seem to treat each definition as a separate word.  So I decided to separate out the words and definitions into two smaller models linked with a `hasMany` eloquent relationship.

```bash
php artisan make:model Word -m
```

Here is the migration - we will be omitting the timestamps from all of our low level models for brevity.  We also need to add an index to the `word` column as we'll be doing a lot of database lookups against this value:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('words', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('words');
    }
}
```

The model is equally simple, with a simple `hasMany` relationship to the `Definition` model that we'll create next:

```php
// app/Word.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
	// remember to turn off timestamps
    public $timestamps = false;

    protected $fillable = ['word'];

    public function definitions()
    {
        return $this->hasMany(Definition::class);
    }
}
```

### The Definition model

```bash
php artisan make:model Definition -m
```

The migration (once again, no timestamps):

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefinitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('definitions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('word_id')->index();
            $table->string('type')->nullable()->default(null);
            $table->text('definition');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('definitions');
    }
}
```

And finally, the model (no need to define the inverse relationship with `Word` at this point):

```php
// app/Definition.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Definition extends Model
{
    public $timestamps = false;

    protected $fillable = ['type', 'definition'];
}
```

### The Dictionary seeder

Now that we have our `Word` and `Definition` models we can get on with seeding those database tables:

```bash
php artisan make:seeder DictionarySeeder
```

Taking a closer look at the source of the OPTED homepage we can see that all of the links to the dictionary pages follow a common format, with only the letter changing between them.  This being the case, we can hard-code the seeder to look for specific URLs to retrieve and crawl:

```php
// database/seeds/DictionarySeeder.php

use Goutte\Client;
use Illuminate\Database\Seeder;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = new Client();
        
        foreach(range('a', 'z') as $letter)
        {
        	$crawler = $client->request('GET', 'http://www.mso.anu.edu.au/~ralph/OPTED/v003/wb1913_' . $letter . 'html');

			// filter through each <p> tag
	        $crawler->filter('p')->each(function ($node) {
	
				// extract the word within <b> tags
	            $word = $node->filter('b')->text();
	
				// extract the type within <i> tags
	            $type  = $node->filter('i')->text();
	
				// clean up the text to give the definition - no need for fany regex
	            $definition = str_replace("{$word} ({$type}) ", '', $node->text());
	
	            $word = \App\Word::firstOrCreate(['word' => $word]);
	
	            $word->definitions()->save(new \App\Definition([
	                'type'  => $type,
	                'definition' => $definition
	            ]));
	        });
       }
    }
}
```

An alternative to using haed-coded URLs would be to parse the homepage first, extracting all of the links that lead to local `.html` files.  This would make the seeder _somewhat_ more futureproof in case of changing URL conventions:

```php
$client = new Client();

$crawler = $client->request('GET', 'http://www.mso.anu.edu.au/~ralph/OPTED/');

$crawler->filter('a')->each(function ($node) use ($client) {

    $href = $node->attr('href');

    if(strpos($href, '.html') !== false)
    {
        // Crawl the letter page here...
    }
});
```


## The Domain Extensions

Ultimately I want to be able to check for the availability of a particular domain via an API call, so it makes sense to limit the range of TLD extensions to those that can be registered by our registrar of choice.

I use Amazon Web Services for a lot of my work - including Route 53 for DNS. You can also search and register domains through Route 53, with a wide range of TLDs available - and since pretty much everything on AWS has an API it's the perfect choice for this little experiment.

[This page](http://docs.aws.amazon.com/Route53/latest/DeveloperGuide/registrar-tld-list.html) lists all of the TLDs available for registration via Route 53, and whilst it may not have the simple markup of OPTED, we can use Goutte to scrape the information that we need.

### The TLD model

```bash
php artisan make:model Tld -m
```

The migration:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tlds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('extension');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tlds');
    }
}
```

The model:

```php
// app/Tld.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tld extends Model
{
    public $timestamps = false;

    protected $fillable = ['extension'];
}

```

### The TLD seeder

```bash
php artisan make:seeder TldSeeder
```
A very quick dive in the page source for the TLD list reveals that all of the extensions can be reached via the following CSS selector `dt > b > span.term`. This seems to be limited to just the TLDs, but just to be on the safe side we can check for a leading period to ensure that we're reading a domain extension and not some other page title.  Finally, the geographic TLDs are followed by their country in the titles, so we'll need to strip those out:

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

        $crawler = $client->request('GET', 'http://docs.aws.amazon.com/Route53/latest/DeveloperGuide/registrar-tld-list.html');

        $crawler->filter('dt > b > span.term')->each(function ($node) {
            $tld = $node->text();

			// Are we looking at a domain extension?
            if(strpos($tld, '.') === 0)
            {
            	// Remove anything that might be after the extension
                $parts = explode(' ', $tld);

                \App\Tld::firstOrCreate([
                    'extension'  => $parts[0],
                ]);
            }
        });
    }
}
```


## The Domains

We're going to seed our database with all of the possible complete-word domains that can be made using the TLD extensions that we have access to.  For easy cross-referencing each domain will belong to both a `Word` and a `Tld`.

### The Domain model

```bash
php artisan make:model Domain -m
```

The migration:

```php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('word_id')->index();
            $table->unsignedInteger('tld_id')->index();
            $table->string('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
```

The model:

```php
// app/Domain.php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    public $timestamps = false;

    protected $fillable = ['domain'];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }

    public function tld()
    {
        return $this->belongsTo(Tld::class);
    }
}
```
At this point we should go back to our `Word` model and add the converse relationship:

```php
// app/Word.php

class Word extends Model
{
 	...
 	
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}
```
We can also do the same with the `Tld` model, but at this stage I don't have any kind of plans to display domains via their TLD.

### The Domain seeder

The domain seeder is going to be pretty simple and methodical.  We're going to cycle through each TLD in turn, remove any periods from the extension and then select all words that end with the resulting string.  We can then substitute the extension back in and persist to the database.

We will also apply a number of other rules in our domain generation:

* Omit words in the dictionary with spaces (we want single words wherever possible)
* Omit words in the dictionary that start with a hyphen (there a number of suffixes in our import)
* Words in the dictionary that have hyphens should get a second domain with the hyphens stripped.  For example _cul-de-sac_ will also get a second pass as _culdesac_.
* Domains that end up having a hyphen directly before the extension should have it stripped.  For example, I know that there is `.blue` extension in our import and the word _Sky-blue_ is in our dictionary - rather than saving `sky-.blue` to the database, we will reduce it down to `sky.blue`.

```bash
php artisan make:seeder DomainSeeder
```

The seeder:

```php
// database/seeds/DomainSeeder.php

use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tlds = \App\Tld::get();

        foreach($tlds as $tld)
        {
        	// Strip out periods from the extension
            $str = str_replace('.', '', $tld->extension);

			// Find all words ending with the extension string
            $words = \App\Word::where('word', 'LIKE', "%{$str}")->get();

            foreach($words as $word)
            {
            	// skip words that have spaces or are suffices
                if(strpos($word->word, ' ') !== false || strpos($word->word,'-') === 0) continue;

				// replace the extension string with the actual extension
                $domain = strtolower(preg_replace('/' . $str . '$/', $tld->extension, $word->word));

				// continue if what we're left with is not the same as the original word or just the extension
                if($domain != strtolower($word->word) && $domain != $tld->extension)
                {
                	// remove any trailing hyphen directly before the extension
                	$domain = str_replace('-.', '.', $domain);
                
                    \App\Domain::create([
                        'word_id'    => $word->id,
                        'tld_id'    => $tld->id,
                        'domain'    => $domain
                    ]);

                    if(strpos($domain, '-') !== false)
                    {
                        \App\Domain::create([
                            'word_id'    => $word->id,
                            'tld_id'    => $tld->id,
                            'domain'    => str_replace('-', '', $domain)
                        ]);
                    }
                }
            }
        }
    }
}
```

## Putting it all together

We could run each seeder individually in sequence using:

```bash
php artisan db:seed --class=[Seeder Class]
```

However, since each seed represents just part of a sequence I prefer to add them all to my `DatabaseSeeder` class:

```php
// database/seeds/DatabaseSeeder.php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call(DictionarySeeder::class);
         $this->call(TldSeeder::class);
         $this->call(DomainSeeder::class);
    }
}
```

Now I can just run my migrations and call all of my seeders:

```bash
php artisan migrate

php artisan db:seed
```

or

```bash
php artisan migrate:refresh --seed
```

At the time of writing, my seeds resulted in the following stats:

* 111,733 words with ~176,205 definitions
* 313 TLD extensions with 13,659 possible domains

**A word of warning - with that many entries the `DictionarySeeder` will understandably take some time to run!**

## A Very Quick UI for Browsing

At this point we can only browse our domains in the database using something like Sequel Pro or phpMyAdmin, so let's build out a really simple UI for the browser.

We'll keep things really simple with one route and one view.  Users will be able to browse each letter of the alphabet to see what words can be made into domains.  First we need a controller to handle everything:

```bash
php artisan make:controller WordsController
```

This controller is going to have a single method to handle all requests:

```php
// app/Http/Controllers/WordsController.php

namespace App\Http\Controllers;

use App\Word;
use Illuminate\Http\Request;

class WordsController extends Controller
{
    public function index($letter = null)
    {
    	// if no letter is passed through it must be the homepage
    	if(empty($letter)) return view('words.index');
    	
    	// only retrieve words that have domains that start with the letter provided, and eager-load the definitions
        $words = Word::has('domains')
        			->with('definitions')
        			->where('word', 'LIKE', "{$letter}%")
        			->paginate();

        return view('words.index', compact('words', 'letter'));
    }
}
```

Next we can create the view at `resources/views/words/index.blade.php`.  For now we'll include our layout in the view, but we can extract that out to separate layout file once we have more views.  Our interface will allow users to select a letter of the alphabet to browse words (and their meanings) that can be made into a domain name.  We'll use Laravel's built-in pagination functionality to make browsing easier.

```html
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

<div class="container">
    <h1 class="page-title text-center"><a href="/">Domain Dictionary</a></h1>

    <p class="lead text-center">Choose a letter to find your next domain!</p>

    <div class="btn-group btn-group-justified" role="group">
        @foreach(range('a','z') as $_letter)
            <a href="/words/{{ $_letter }}" class="btn btn-default @if(isset($letter) && strtolower($letter) == $_letter) active @endif">{{ strtoupper($_letter) }}</a>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <hr>
            @if( ! empty($words))
                <div class="text-center">
                    {!! $words->links() !!}
                </div>
                @foreach($words as $word)
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h3>{{ $word->word }}</h3>

                            @foreach($word->definitions as $definition)
                                <p><em class="text-muted">{{ $definition->type }}</em> {{ $definition->definition }}</p>
                            @endforeach

                            @foreach($word->domains as $domain)
                                <p><a href="http://{{ $domain->domain }}" target="_blank">{{ $domain->domain }}</a></p>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="text-center">
                    {!! $words->links() !!}
                </div>
            @endif
        </div>
    </div>
</div>
</body>
</html>
```

Finally we need to wire it all up with a couple of routes:

```php
// routes/web.php

Route::get('/', 'WordsController@index');
Route::get('words/{letter}', 'WordsController@index');

```
## The End Result

![](/assets/img/snapstack/1/LcLbXMVVQ8dmIudlSroEhh8LSOBfJlNEr8ygdOzT.png)

And there we have it - it's not going to win any design awards but it has achieved everything I set out to do in the first instance:

* Pull in a complete dictionary of words (with some web-scraping magic)
* Pull in a list of available TLDs from my registrar of choice
* Generate a list of all possible domains that can be created that are complete words

My next steps will be to use the Route 53 API to check the availability of domains.  Whether this is done on-demand or ahead of time _en masse_ I haven't quite decided yet, but I'll write about it here as I work my way through it.

I need to get back to my day job now, but in the meantime you can view the source code for this [on Github](https://github.com/theprivateer/domain-dictionary) and browse the end result at [https://github.com/theprivateer/domain-dictionary](https://github.com/theprivateer/domain-dictionary).
