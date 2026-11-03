<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listPages(Request $request, string $lang): array
    {
        $ver = $this->cache->version('pages');
        $cacheKey = "pages_list:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, static function () use ($request): array {
            $pages = Page::query()
                ->where('is_published', true)
                ->whereNotIn('slug', ['home'])
                ->latest('updated_at')
                ->get();

            return PageResource::collection($pages)->resolve($request);
        });
    }

    public function findPage(Request $request, string $slugKey, string $lang): ?array
    {
        if ($slugKey === 'invalid') {
            return null;
        }

        $cacheKey = "page_{$slugKey}_{$lang}";

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $page = Page::where('slug', $slugKey)->where('is_published', true)->first();

            if (! $page) {
                return null;
            }

            $data = (new PageResource($page))->resolve($request);
            $this->cache->put($cacheKey, $data, 3600);
        }

        return $data;
    }
}
