<?php

namespace App\Models;

use TightenCo\Jigsaw\Collection\CollectionItem;

class DevPost extends CollectionItem
{
    public function getCollection(): string
    {
        return 'dev';
    }
}
