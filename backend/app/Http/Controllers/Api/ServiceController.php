<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ServiceCatalogApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends BaseApiController
{
    public function __construct(
        private readonly ServiceCatalogApiService $serviceCatalog
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->serviceCatalog->listServices($request, $this->getLang()));
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $data = $this->serviceCatalog->findService(
            request: $request,
            slug: $slug,
            slugKey: $this->sanitizeSlugParam($slug),
            lang: $this->getLang(),
        );

        return response()->json(['data' => $data]);
    }
}
