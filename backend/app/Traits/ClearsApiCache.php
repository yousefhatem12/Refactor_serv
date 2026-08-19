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

    /**
     * Register cache-clearing listeners via the Eloquent event dispatcher.
     * Called automatically by Eloquent's Model::boot() via the bootXxx() convention.
     */
    protected static function bootClearsApiCache(): void
    {
        // Eloquent fires "eloquent.saved: App\Models\Foo" and
        // "eloquent.deleted: App\Models\Foo" internally — listening to
        // those strings is exactly equivalent to Model::saved() / deleted()
        // but uses only the Event facade which IDEs understand perfectly.
        $model = static::class;

        $flush = static function () use ($model): void {
            foreach ($model::apiCacheKeys() as $key) {
                Cache::forget($key);
            }
        };

        Event::listen("eloquent.saved: {$model}", $flush);
        Event::listen("eloquent.deleted: {$model}", $flush);
    }
}
