<?php

namespace App\Contracts\Api;

use Closure;

interface ApiCacheServiceInterface
{
    public function version(string $scope): int;

    public function remember(string $key, Closure $callback, int $ttlSeconds = 3600): mixed;

    public function get(string $key): mixed;

    public function put(string $key, mixed $value, int $ttlSeconds = 3600): void;

    public function forget(string $key): void;
}
