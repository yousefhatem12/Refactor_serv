<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\PageApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends BaseApiController
{
    public function __construct(
        private readonly PageApiService $pageService
    ) {}

    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->pageService->listPages($request, $this->getLang()),
        ]);
    }

    public function show(Request $request, $slug): JsonResponse
    {
        $data = $this->pageService->findPage(
            request: $request,
            slugKey: $this->sanitizeSlugParam($slug),
            lang: $this->getLang(),
        );

        if ($data === null) {
            return response()->json(['message' => 'Page not found'], 404);
        }

        return response()->json(['data' => $data]);
    }
}
