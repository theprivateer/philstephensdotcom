<?php

namespace App\Listeners;

use TightenCo\Jigsaw\Jigsaw;

class PublishHiddenFiles
{
    protected $files = [
        './_redirects',
    ];

    public function handle(Jigsaw $jigsaw)
    {
        foreach($this->files as $file) {
            if(file_exists(__DIR__ . '/../source/' . $file)) {
                file_put_contents(
                    $jigsaw->getDestinationPath() . '/' . $file,
                    file_get_contents(__DIR__ . '/../source/' . $file)
                );
            }
        }
    }
}
