<?php

namespace App\Models;

use TightenCo\Jigsaw\Collection\CollectionItem;

class Post extends CollectionItem
{
    public function getCollection(): string
    {
        return 'blog';
    }
}
