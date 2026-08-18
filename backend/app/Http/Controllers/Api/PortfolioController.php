<?php

namespace App\Http\Controllers\Api;

use App\Models\Project;
use App\Models\ProjectCategory;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PortfolioController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $limit = $this->limit($request->query('limit'));
        $category = $request->query('category');
        $featured = $request->query('featured');
        $page = $this->pageNumber($request);
        $perPage = $this->perPage($request, 9);

        $cacheKey = "portfolio_{$lang}_cat_" . ($category ?: 'all') . "_feat_" . ($featured ?: 'false') . "_limit_" . ($limit ?: 'none') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = Cache::remember($cacheKey, 3600, function () use ($lang, $limit, $category, $featured, $page, $perPage) {
            $query = Project::orderBy('id', 'desc');

            if ($category && strtolower($category) !== 'all') {
                $query->where('category', strtolower($category));
            }

            if ($featured === 'true' || $featured === '1') {
                $query->where('is_featured', true);
            }

            if ($limit) {
                $projects = $query->take((int) $limit)->get();
                return [
                    'type' => 'flat',
                    'data' => ProjectResource::collection($projects)->resolve()
                ];
            }

            if ($page) {
                $projectsPaginated = $query->paginate($perPage);
                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => ProjectResource::collection($projectsPaginated->items())->resolve(),
                        'current_page' => $projectsPaginated->currentPage(),
                        'last_page' => $projectsPaginated->lastPage(),
                        'has_more' => $projectsPaginated->hasMorePages(),
                    ]
                ];
            }

            $projects = $query->get();
            return [
                'type' => 'flat',
                'data' => ProjectResource::collection($projects)->resolve()
            ];
        });

        return response()->json($response['data']);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $cacheKey = "project_categories_{$lang}";

        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return ProjectCategory::all()->map(function ($cat) use ($lang) {
                return [
                    'id' => $cat->slug,
                    'label' => $this->trans($cat->name, $lang),
                ];
            });
        });
        return response()->json($categories);
    }

    public function show(string|int $idOrSlug)
    {
        $lang = $this->getLang();
        $cacheKey = "project_{$idOrSlug}_{$lang}";

        $project = Cache::remember($cacheKey, 3600, function () use ($idOrSlug) {
            return Project::where('slug', $idOrSlug)
                ->orWhere('id', $idOrSlug)
                ->firstOrFail();
        });

        return new ProjectResource($project);
    }
}
