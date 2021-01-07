<?php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;
use Intervention\Image\ImageManagerStatic as Image;

class ResizeImages
{
	public function handle(Jigsaw $jigsaw)
	{
	    return;

		// Cycle through all images in $jigsaw->getDestinationPath() . '/assets/img'
		// Construct the iterator
		$it = new \RecursiveDirectoryIterator($jigsaw->getDestinationPath() . '/assets/img');
		
		// Loop through files
		foreach(new \RecursiveIteratorIterator($it) as $file) {
			if(@is_array(getimagesize($file))){
				$img = Image::make($file);
				
				if($img->width() > 850)
				{
					$img->widen(850, function ($constraint) {
						$constraint->upsize();
					});
				
					// Save image...
					$img->save($file->getPathname());
						
				}
			}
			
		}
	}
}