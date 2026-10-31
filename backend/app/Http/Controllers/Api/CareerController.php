<?php

namespace App\Http\Controllers\Api;

use App\Models\Career;
use App\Models\CareerApplication;
use App\Models\User;
use App\Http\Resources\CareerResource;
use App\Http\Requests\StoreCareerApplicationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

class CareerController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $department = $request->query('department');
        $search = $request->query('search');
        $page = $this->pageNumber($request, 1);
        $perPage = $this->perPage($request, 12);
        $ver = $this->cacheVersion('careers');

        $deptKey = $this->sanitizeSlugParam($department);
        $searchClean = is_string($search) ? trim(mb_substr($search, 0, 100)) : '';

        $buildResponse = function () use ($department, $searchClean, $page, $perPage, $request, $ver) {
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

            $uniqueDepts = Cache::remember("careers:v{$ver}:departments", 3600, static function () {
                return Career::where('is_active', true)->distinct()->pluck('department')->filter()->values()->toArray();
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
            return response()->json($buildResponse());
        }

        $cacheKey = "careers:v{$ver}:{$lang}:dept_{$deptKey}:page_{$page}:per_{$perPage}";
        $data = Cache::remember($cacheKey, 3600, $buildResponse);

        return response()->json($data);
    }

    public function show(Request $request, int $id)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('careers');
        $cacheKey = "careers:v{$ver}:show:{$id}:{$lang}";

        $data = Cache::remember($cacheKey, 3600, function () use ($id, $request) {
            $career = Career::where('is_active', true)->find($id);
            if (! $career) {
                return null;
            }

            return (new CareerResource($career))->resolve($request);
        });

        if ($data === null) {
            Cache::forget($cacheKey);
            return response()->json(['message' => 'Career not found'], 404);
        }

        return response()->json(['data' => $data]);
    }

    public function apply(StoreCareerApplicationRequest $request)
    {
        $validated = $request->validated();
        $app = CareerApplication::create($validated);

        $admins = Cache::remember('super_admins_notify', 300, fn () => User::role('super_admin')->get());

        // Notify admins after the response is sent — decouples notification from form success.
        app()->terminating(static function () use ($app, $admins, $validated): void {
            Notification::make()
                ->title('New Career Application')
                ->body("You have received a new application from {$validated['name']} for the position: " . ($app->career?->title['en'] ?? 'Job'))
                ->icon('heroicon-o-briefcase')
                ->success()
                ->sendToDatabase($admins);
        });

        return response()->json(['message' => 'Application submitted successfully', 'reference_id' => $app->id], 201);
    }
}
