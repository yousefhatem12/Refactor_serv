<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

/**
 * Automatically clears API cache entries when a model is saved or deleted.
 *
 * Usage: Add `use ClearsApiCache;` to any Eloquent Model, then override
 * `apiCacheKeys()` to return the list of cache keys this model's data
 * appears in.
 *
 * Implementation note: we register listeners via Laravel's Event dispatcher
 * using the "eloquent.saved: {Model}" string format instead of calling
 * static::saved() / static::deleted() — those Eloquent helper methods are
 * not declared on the Filesystem contract or any interface, so IDE tools
 * (Intelephense, PhpStorm) report them as "Undefined method" in trait
 * context regardless of @mixin or @phpstan-require-extends annotations.
 *
 * @phpstan-require-extends \Illuminate\Database\Eloquent\Model
 */
trait ClearsApiCache
{
    protected static function apiCacheKeys(): array
    {
        return [];
    }

    protected static function apiCacheScopes(): array
    {
        return [];
    }

    public static function cacheVersion(string $scope): int
    {
        return (int) Cache::rememberForever("api_cache_version:{$scope}", static fn () => 1);
    }

    public static function bumpCacheVersion(string $scope): void
    {
        $key = "api_cache_version:{$scope}";
        $current = (int) Cache::get($key, 1);
        Cache::forever($key, $current + 1);
    }

    /**
     * Register cache-clearing listeners via the Eloquent event dispatcher.
     * Called automatically by Eloquent's Model::boot() via the bootXxx() convention.
     */
    protected static function bootClearsApiCache(): void
    {
        $model = static::class;

        $flush = static function () use ($model): void {
            foreach ($model::apiCacheKeys() as $key) {
                Cache::forget($key);
            }
            foreach ($model::apiCacheScopes() as $scope) {
                self::bumpCacheVersion($scope);
            }
        };

        Event::listen("eloquent.saved: {$model}", $flush);
        Event::listen("eloquent.deleted: {$model}", $flush);
    }
}
