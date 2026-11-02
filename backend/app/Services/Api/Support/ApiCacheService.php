<?php

namespace App\Services\Api\Support;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Traits\ClearsApiCache;
use Closure;
use Illuminate\Support\Facades\Cache;

class ApiCacheService implements ApiCacheServiceInterface
{
    public function version(string $scope): int
    {
        return ClearsApiCache::cacheVersion($scope);
    }

    public function remember(string $key, Closure $callback, int $ttlSeconds = 3600): mixed
    {
        return Cache::remember($key, $ttlSeconds, $callback);
    }

    public function get(string $key): mixed
    {
        return Cache::get($key);
    }

    public function put(string $key, mixed $value, int $ttlSeconds = 3600): void
    {
        Cache::put($key, $value, $ttlSeconds);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }
}
