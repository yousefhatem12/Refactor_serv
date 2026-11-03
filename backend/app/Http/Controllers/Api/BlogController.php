<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\BlogApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends BaseApiController
{
    public function __construct(
        private readonly BlogApiService $blogService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $categoryRaw = $request->query('category_raw') ?? $request->query('category');

        $payload = $this->blogService->listPosts(
            request: $request,
            lang: $this->getLang(),
            limit: $this->limit($request->query('limit')),
            categoryKey: $this->sanitizeSlugParam($categoryRaw),
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        );

        return response()->json($payload);
    }

    public function categories(): JsonResponse
    {
        return response()->json($this->blogService->listCategories($this->getLang()));
    }

    public function trending(): JsonResponse
    {
        return response()->json($this->blogService->listTrending($this->getLang()));
    }

    public function show(Request $request, string|int $idOrSlug): JsonResponse
    {
        $data = $this->blogService->findPost(
            request: $request,
            idOrSlug: $idOrSlug,
            slugKey: $this->sanitizeSlugParam($idOrSlug),
            lang: $this->getLang(),
        );

        return response()->json(['data' => $data]);
    }
}
