<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Support\LocalizationHelper;
use Illuminate\Http\Request;

class PortfolioApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listProjects(
        Request $request,
        string $lang,
        ?int $limit,
        string $categoryKey,
        bool $isFeatured,
        int $page,
        int $perPage
    ): array {
        if ($categoryKey === 'invalid') {
            return [
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ];
        }

        $ver = $this->cache->version('projects');
        $featKey = $isFeatured ? '1' : '0';
        $cacheKey = "portfolio:v{$ver}:{$lang}:cat_{$categoryKey}:feat_{$featKey}:limit_" . ($limit ?: 'none') . ":page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, function () use ($limit, $categoryKey, $isFeatured, $page, $perPage, $request): array {
            $query = Project::select(['id', 'slug', 'name', 'category', 'description', 'image', 'client', 'url', 'tags', 'is_featured'])
                ->orderBy('id', 'desc');

            if ($categoryKey !== 'all') {
                $query->where('category', $categoryKey);
            }

            if ($isFeatured) {
                $query->where('is_featured', true);
            }

            if ($limit) {
                $projects = $query->take($limit)->get();

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
    }

    public function listCategories(string $lang): array
    {
        $ver = $this->cache->version('projects');
        $cacheKey = "project_categories:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, function () use ($lang): array {
            return ProjectCategory::select(['id', 'name', 'slug'])
                ->get()
                ->map(static fn (ProjectCategory $cat): array => [
                    'id' => $cat->slug,
                    'label' => LocalizationHelper::trans($cat->name, $lang),
                ])
                ->toArray();
        });
    }

    public function findProject(Request $request, string|int $idOrSlug, string $slugKey, string $lang): array
    {
        if ($slugKey === 'invalid') {
            abort(404);
        }

        $ver = $this->cache->version('projects');
        $cacheKey = "project:v{$ver}:{$slugKey}:{$lang}";

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $project = Project::where(function ($q) use ($idOrSlug) {
                $q->where('slug', $idOrSlug)
                    ->orWhere('id', $idOrSlug);
            })->firstOrFail();

            $data = (new ProjectResource($project))->resolve($request);
            $this->cache->put($cacheKey, $data, 3600);
        }

        return $data;
    }
}
