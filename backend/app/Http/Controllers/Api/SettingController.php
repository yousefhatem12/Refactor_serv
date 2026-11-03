<?php

namespace App\Http\Controllers\Api;

use App\Services\Api\SiteSettingsApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends BaseApiController
{
    public function __construct(
        private readonly SiteSettingsApiService $settingsService
    ) {}

    public function settings(): JsonResponse
    {
        return response()->json($this->settingsService->getSettings($this->getLang()));
    }

    public function testimonials(Request $request): JsonResponse
    {
        return response()->json($this->settingsService->getTestimonials($request, $this->getLang()));
    }

    public function team(Request $request): JsonResponse
    {
        return response()->json($this->settingsService->getTeamMembers($request, $this->getLang()));
    }

    public function features(Request $request): JsonResponse
    {
        return response()->json($this->settingsService->getFeatures($request, $this->getLang()));
    }

    public function footer(): JsonResponse
    {
        return response()->json($this->settingsService->getFooterData($this->getLang()));
    }

    public function navLinks(Request $request): JsonResponse
    {
        return response()->json($this->settingsService->getNavLinks($request, $this->getLang()));
    }

    public function pricingPlans(Request $request): JsonResponse
    {
        $serviceIdRaw = $request->query('service_id');

        return response()->json($this->settingsService->getPricingPlans(
            request: $request,
            lang: $this->getLang(),
            serviceIdRaw: $serviceIdRaw,
            serviceKey: $this->sanitizeSlugParam($serviceIdRaw),
        ));
    }

    public function bankAccounts(Request $request): JsonResponse
    {
        $countryRaw = $request->query('country');

        return response()->json($this->settingsService->getBankAccounts(
            request: $request,
            lang: $this->getLang(),
            countryRaw: $countryRaw,
            country: $this->sanitizeCountryParam($countryRaw),
            page: $this->pageNumber($request, 1),
            perPage: $this->perPage($request, 12),
        ));
    }
}
