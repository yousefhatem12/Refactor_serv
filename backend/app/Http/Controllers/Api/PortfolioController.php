<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\PortfolioApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortfolioController extends BaseApiController
{
    public function __construct(
        private readonly PortfolioApiService $portfolioService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $featuredRaw = $request->query('featured');
        $isFeatured = ($featuredRaw === 'true' || $featuredRaw === '1');

        $payload = $this->portfolioService->listProjects(
            request: $request,
            lang: $this->getLang(),
            limit: $this->limit($request->query('limit')),
            categoryKey: $this->sanitizeSlugParam($request->query('category')),
            isFeatured: $isFeatured,
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        );

        return response()->json($payload);
    }

    public function categories(): JsonResponse
    {
        return response()->json($this->portfolioService->listCategories($this->getLang()));
    }

    public function show(Request $request, string|int $idOrSlug): JsonResponse
    {
        $data = $this->portfolioService->findProject(
            request: $request,
            idOrSlug: $idOrSlug,
            slugKey: $this->sanitizeSlugParam($idOrSlug),
            lang: $this->getLang(),
        );

        return response()->json(['data' => $data]);
    }
}
