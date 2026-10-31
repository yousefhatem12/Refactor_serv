<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ServiceController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('services');
        $cacheKey = "services:v{$ver}:{$lang}";

        $mapped = Cache::remember($cacheKey, 3600, function () use ($request) {
            $services = Service::where('is_active', true)
                ->select(['id', 'name', 'slug', 'description', 'icon', 'is_active'])
                ->get();

            return ServiceResource::collection($services)->resolve($request);
        });

        return response()->json($mapped);
    }

    public function show(Request $request, string $slug)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('services');
        $slugKey = $this->sanitizeSlugParam($slug);

        if ($slugKey === 'invalid') {
            abort(404);
        }

        $cacheKey = "service_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = Cache::get($cacheKey);
        if ($data === null) {
            $service = Service::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug)
                        ->orWhere('id', $slug);
                })
                ->firstOrFail();

            $data = (new ServiceResource($service))->resolve($request);
            Cache::put($cacheKey, $data, 3600);
        }

        return response()->json(['data' => $data]);
    }
}
