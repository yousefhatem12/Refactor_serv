<?php

namespace App\Http\Controllers\Api;

use App\Models\Page;
use App\Http\Resources\PageResource;
use Illuminate\Support\Facades\Cache;

class PageController extends BaseApiController
{
    public function show($slug)
    {
        $lang = $this->getLang();
        $cacheKey = "page_{$slug}_{$lang}";

        $page = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return Page::where('slug', $slug)->first();
        });

        if (!$page) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return new PageResource($page);
    }
}
