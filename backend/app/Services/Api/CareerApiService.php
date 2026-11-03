<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Contracts\Notifications\AdminNotificationServiceInterface;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use App\Models\CareerApplication;
use Illuminate\Http\Request;

class CareerApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache,
        private readonly AdminNotificationServiceInterface $notifications
    ) {}

    public function listCareers(
        Request $request,
        string $lang,
        mixed $department,
        string $deptKey,
        string $searchClean,
        int $page,
        int $perPage
    ): array {
        $ver = $this->cache->version('careers');

        $buildResponse = function () use ($department, $searchClean, $page, $perPage, $request, $ver): array {
            $query = Career::where('is_active', true)
                ->select(['id', 'department', 'title', 'location', 'salary'])
                ->orderBy('id', 'desc');

            if ($department && strtolower((string) $department) !== 'all') {
                $query->where('department', $department);
            }

            if ($searchClean !== '') {
                $escaped = addcslashes(strtolower($searchClean), '%_\\');
                $query->where(function ($q) use ($escaped) {
                    $q->where('title->en', 'like', "%{$escaped}%")
                        ->orWhere('title->ar', 'like', "%{$escaped}%")
                        ->orWhere('description->en', 'like', "%{$escaped}%")
                        ->orWhere('description->ar', 'like', "%{$escaped}%")
                        ->orWhere('department', 'like', "%{$escaped}%");
                });
            }

            $uniqueDepts = $this->cache->remember("careers:v{$ver}:departments", static function (): array {
                return Career::where('is_active', true)
                    ->distinct()
                    ->pluck('department')
                    ->filter()
                    ->values()
                    ->toArray();
            });

            $careersPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => CareerResource::collection($careersPaginated->items())->resolve($request),
                'current_page' => $careersPaginated->currentPage(),
                'last_page' => $careersPaginated->lastPage(),
                'total' => $careersPaginated->total(),
                'has_more' => $careersPaginated->hasMorePages(),
                'departments' => $uniqueDepts,
            ];
        };

        if ($searchClean !== '') {
            return $buildResponse();
        }

        $cacheKey = "careers:v{$ver}:{$lang}:dept_{$deptKey}:page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, $buildResponse);
    }

    public function findCareer(Request $request, int $id, string $lang): ?array
    {
        $ver = $this->cache->version('careers');
        $cacheKey = "careers:v{$ver}:show:{$id}:{$lang}";

        $data = $this->cache->remember($cacheKey, static function () use ($id, $request): ?array {
            $career = Career::where('is_active', true)->find($id);
            if (! $career) {
                return null;
            }

            return (new CareerResource($career))->resolve($request);
        });

        if ($data === null) {
            $this->cache->forget($cacheKey);
        }

        return $data;
    }

    public function submitApplication(array $validated): CareerApplication
    {
        $app = CareerApplication::create($validated);
        $positionTitle = $app->career?->title['en'] ?? 'Job';

        $this->notifications->notifySuperAdminsAfterResponse(
            'New Career Application',
            "You have received a new application from {$validated['name']} for the position: {$positionTitle}",
            'heroicon-o-briefcase'
        );

        return $app;
    }
}
