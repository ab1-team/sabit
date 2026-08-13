<?php

declare(strict_types=1);

namespace App\Cache;

use Illuminate\Cache\DatabaseStore;
use Illuminate\Cache\TaggedCache;
use Illuminate\Cache\TagSet;

class TaggedDatabaseStore extends DatabaseStore
{
    public function tags($names)
    {
        $names = is_array($names) ? $names : func_get_args();

        return new TaggedCache($this, new TagSet($this, $names));
    }
}
