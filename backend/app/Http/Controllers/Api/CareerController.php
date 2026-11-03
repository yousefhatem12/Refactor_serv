<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCareerApplicationRequest;
use App\Services\Api\CareerApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CareerController extends BaseApiController
{
    public function __construct(
        private readonly CareerApiService $careerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $department = $request->query('department');
        $search = $request->query('search');
        $searchClean = is_string($search) ? trim(mb_substr($search, 0, 100)) : '';

        $payload = $this->careerService->listCareers(
            request: $request,
            lang: $this->getLang(),
            department: $department,
            deptKey: $this->sanitizeSlugParam($department),
            searchClean: $searchClean,
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        );

        return response()->json($payload);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $data = $this->careerService->findCareer($request, $id, $this->getLang());

        if ($data === null) {
            return response()->json(['message' => 'Career not found'], 404);
        }

        return response()->json(['data' => $data]);
    }

    public function apply(StoreCareerApplicationRequest $request): JsonResponse
    {
        $app = $this->careerService->submitApplication($request->validated());

        return response()->json([
            'message' => 'Application submitted successfully',
            'reference_id' => $app->id,
        ], 201);
    }
}
