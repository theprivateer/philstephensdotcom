---
extends: _layouts.dev
section: content
date: 2020-11-13
title: Image Optimisation with Jigsaw
---
# Image Optimisation with Jigsaw

**EDIT: Since writing this post I have decided to remove the featured images from my website templates (just a preference thing), but the following is still valid.**

I use [Jigsaw](https://jigsaw.tighten.co) to build this blog, and each post leads with a large featured image.  In many cases these images are just thematic and come from [Unsplash](https://unsplash.com), but my writing and publishing workflow doesn't include any image manipulation software to optimise the dimensions for the web.

Using Jigsaw's [event listeners](https://jigsaw.tighten.co/docs/event-listeners/) I decided to build a super simple post-build script to reduce the overall size of any images that were unnecessarily large for my site template.

Jigsaw's event listeners allow you to hook into key points of the build and run custom code.  This can either be defined as an inline closure or a separate class.  I have opted for a separate class, but it's not particularly complicated so could easily be inlined if you prefer.

For the image manipulation we'll be using [Intervention Image](http://image.intervention.io/) library, so pull that into your project using Composer:

```bash
composer require intervention/image
```

Next up add the following file to the `listeners` directory in the project root:

```php
// listeners/ResizeImages.php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;
use Intervention\Image\ImageManagerStatic as Image;

class ResizeImages
{
	public function handle(Jigsaw $jigsaw)
	{
		// You can inline these values if you prefer
		$pathToImages = '/assets/img';
		$maxImageWidth = 850;
	
		// Construct the iterator
		$it = new \RecursiveDirectoryIterator($jigsaw->getDestinationPath() . $pathToImages);
		
		// Loop through files
		foreach(new \RecursiveIteratorIterator($it) as $file) {
			// Simple check to see if the file is an image
			if(@is_array(getimagesize($file))) {
				
				// Instantiate an Intervention Image instance
				$img = Image::make($file);
				
				if($img->width() > $maxImageWidth)
				{
					$img->widen($maxImageWidth, function ($constraint) {
						$constraint->upsize();
					});
				
					// Save image back to the original location
					$img->save($file->getPathname());
						
				}
			}
			
		}
	}
}
```

It's a fairly simple setup, using PHP native classes to iterate over the contents of your build directory.  My blog template (at time of writing) maxes-out at around 850 pixels wide, so I'm using that as my limit for image width.  Of course this doesn't take account of high density/retina displays, so you could double that for extra image clarity on devices that support it.  We also do not want to upsize any images that are already below the threshold, so add `$constraint->upsize();` to the closure in Interventions `widen` method.

In this example I am targeting an `img` directory inside my `assets` directory as I know that is where all of the images will be - you can adapt the code to check multiple locations, or even scan the entire build directory if you prefer.

Finally, we register this class in the `bootstrap.php` file:

```php
// bootstrap.php

$events->afterBuild(App\Listeners\ResizeImages::class);
```

When we build our project now, all of the images in the build directory will be optimised for our particular site template.  You could take this further and add further steps using Interventions various methods (adjust image quality, for example, to further reduce file size).  I like this approach as it is non-destructive to the original source images, so I can change this build-optimisation at any time if I update my site template.
