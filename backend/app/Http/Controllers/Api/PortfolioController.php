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
        $categoryKey = $this->sanitizeSlugParam($category);
        $featuredRaw = $request->query('featured');
        $isFeatured = ($featuredRaw === 'true' || $featuredRaw === '1');
        $page = $this->pageNumber($request, 1);
        $perPage = $this->perPage($request, 12);
        $ver = $this->cacheVersion('projects');

        if ($categoryKey === 'invalid') {
            return response()->json([
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ]);
        }

        $featKey = $isFeatured ? '1' : '0';
        $cacheKey = "portfolio:v{$ver}:{$lang}:cat_{$categoryKey}:feat_{$featKey}:limit_" . ($limit ?: 'none') . ":page_{$page}:per_{$perPage}";

        $response = Cache::remember($cacheKey, 3600, function () use ($limit, $categoryKey, $isFeatured, $page, $perPage, $request) {
            $query = Project::select(['id', 'slug', 'name', 'category', 'description', 'image', 'client', 'url', 'tags', 'is_featured'])
                ->orderBy('id', 'desc');

            if ($categoryKey !== 'all') {
                $query->where('category', $categoryKey);
            }

            if ($isFeatured) {
                $query->where('is_featured', true);
            }

            if ($limit) {
                $projects = $query->take((int) $limit)->get();
                return ProjectResource::collection($projects)->resolve($request);
            }

            $projectsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => ProjectResource::collection($projectsPaginated->items())->resolve($request),
                'current_page' => $projectsPaginated->currentPage(),
                'last_page' => $projectsPaginated->lastPage(),
                'total' => $projectsPaginated->total(),
                'has_more' => $projectsPaginated->hasMorePages(),
            ];
        });

        return response()->json($response);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('projects');
        $cacheKey = "project_categories:v{$ver}:{$lang}";

        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return ProjectCategory::select(['id', 'name', 'slug'])->get()->map(function ($cat) use ($lang) {
                return [
                    'id' => $cat->slug,
                    'label' => $this->trans($cat->name, $lang),
                ];
            })->toArray();
        });
        return response()->json($categories);
    }

    public function show(Request $request, string|int $idOrSlug)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('projects');
        $slugKey = $this->sanitizeSlugParam($idOrSlug);

        if ($slugKey === 'invalid') {
            abort(404);
        }

        $cacheKey = "project:v{$ver}:{$slugKey}:{$lang}";

        $data = Cache::get($cacheKey);
        if ($data === null) {
            $project = Project::where(function ($q) use ($idOrSlug) {
                $q->where('slug', $idOrSlug)
                  ->orWhere('id', $idOrSlug);
            })->firstOrFail();

            $data = (new ProjectResource($project))->resolve($request);
            Cache::put($cacheKey, $data, 3600);
        }

        return response()->json(['data' => $data]);
    }
}
