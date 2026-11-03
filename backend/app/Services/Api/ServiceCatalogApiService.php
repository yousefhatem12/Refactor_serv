<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceCatalogApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listServices(Request $request, string $lang): array
    {
        $ver = $this->cache->version('services');
        $cacheKey = "services:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, function () use ($request): array {
            $services = Service::where('is_active', true)
                ->select(['id', 'name', 'slug', 'description', 'icon', 'is_active'])
                ->get();

            return ServiceResource::collection($services)->resolve($request);
        });
    }

    public function findService(Request $request, string $slug, string $slugKey, string $lang): array
    {
        if ($slugKey === 'invalid') {
            abort(404);
        }

        $ver = $this->cache->version('services');
        $cacheKey = "service_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $service = Service::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug)
                        ->orWhere('id', $slug);
                })
                ->firstOrFail();

            $data = (new ServiceResource($service))->resolve($request);
            $this->cache->put($cacheKey, $data, 3600);
        }

        return $data;
    }
}
