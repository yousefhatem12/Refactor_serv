<?php

namespace App\Http\Controllers\Api;

use App\Models\Page;
use App\Http\Resources\PageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('pages');
        $cacheKey = "pages_list:v{$ver}:{$lang}";

        $data = Cache::remember($cacheKey, 3600, function () use ($request) {
            $pages = Page::query()
                ->where('is_published', true)
                ->whereNotIn('slug', ['home'])
                ->latest('updated_at')
                ->get();

            return PageResource::collection($pages)->resolve($request);
        });

        return response()->json(['data' => $data]);
    }

    public function show(Request $request, $slug)
    {
        $lang = $this->getLang();
        $slugKey = $this->sanitizeSlugParam($slug);

        if ($slugKey === 'invalid') {
            return response()->json(['message' => 'Page not found'], 404);
        }

        $cacheKey = "page_{$slugKey}_{$lang}";

        $data = Cache::get($cacheKey);
        if ($data === null) {
            $page = Page::where('slug', $slugKey)->where('is_published', true)->first();

            if (! $page) {
                return response()->json(['message' => 'Page not found'], 404);
            }

            $data = (new PageResource($page))->resolve($request);
            Cache::put($cacheKey, $data, 3600);
        }

        return response()->json(['data' => $data]);
    }
}
