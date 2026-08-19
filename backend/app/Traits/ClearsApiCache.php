<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

/**
 * Automatically clears API cache entries when a model is saved or deleted.
 *
 * Usage: Add `use ClearsApiCache;` to any Model, then override `apiCacheKeys()`
 * to return the list of cache keys this model's data appears in.
 */
trait ClearsApiCache
{
    protected static function apiCacheKeys(): array
    {
        return [];
    }

    protected static function bootClearsApiCache(): void
    {
        $flush = static function (): void {
            foreach (static::apiCacheKeys() as $key) {
                Cache::forget($key);
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }
}
