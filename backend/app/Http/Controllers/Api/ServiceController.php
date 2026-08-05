<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class ServiceController extends BaseApiController
{
    public function index()
    {
        $lang = $this->getLang();
        $cacheKey = "services_v2_{$lang}";

        $mapped = Cache::remember($cacheKey, 3600, function () {
            $services = Service::where('is_active', true)->get();

            return ServiceResource::collection($services);
        });

        return response()->json($mapped);
    }

    public function show(string $slug)
    {
        $service = Service::where('is_active', true)
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->orWhere('id', $slug);
            })
            ->firstOrFail();

        return new ServiceResource($service);
    }
}
