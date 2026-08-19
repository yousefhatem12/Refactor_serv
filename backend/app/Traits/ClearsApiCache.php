<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Automatically clears API cache entries when a model is saved or deleted.
 *
 * Usage: Add `use ClearsApiCache;` to any Eloquent Model, then override
 * `apiCacheKeys()` to return the list of cache keys this model's data
 * appears in.
 *
 * @phpstan-require-extends Model
 */
trait ClearsApiCache
{
    protected static function apiCacheKeys(): array
    {
        return [];
    }

    /**
     * Register cache-clearing listeners.
     * Called automatically by Eloquent's Model::boot() via the bootXxx() convention.
     */
    protected static function bootClearsApiCache(): void
    {
        /** @var class-string<static> $self */
        $self = static::class;

        $flush = static function (): void {
            foreach (static::apiCacheKeys() as $key) {
                Cache::forget($key);
            }
        };

        $self::saved($flush);
        $self::deleted($flush);
    }
}
