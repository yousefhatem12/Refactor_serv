<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\FaqApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    public function __construct(
        private readonly FaqApiService $faqService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $searchClean = is_string($search) ? trim(mb_substr($search, 0, 100)) : '';

        $payload = $this->faqService->listFaqs(
            request: $request,
            lang: $this->getLang(),
            searchClean: $searchClean,
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        );

        return response()->json($payload);
    }
}
