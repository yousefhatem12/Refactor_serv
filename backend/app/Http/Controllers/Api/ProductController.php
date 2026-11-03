<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\ProductApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function __construct(
        private readonly ProductApiService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $category = $request->query('category');

        $payload = $this->productService->listProducts(
            request: $request,
            lang: $this->getLang(),
            category: $category,
            categoryKey: $this->sanitizeSlugParam($category),
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        );

        return response()->json($payload);
    }

    public function categories(): JsonResponse
    {
        return response()->json($this->productService->listCategories($this->getLang()));
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $data = $this->productService->findProduct(
            request: $request,
            slug: $slug,
            slugKey: $this->sanitizeSlugParam($slug),
            lang: $this->getLang(),
        );

        return response()->json(['data' => $data]);
    }
}
