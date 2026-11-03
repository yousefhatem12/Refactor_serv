<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\PostResource;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Support\LocalizationHelper;
use Illuminate\Http\Request;

class BlogApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listPosts(
        Request $request,
        string $lang,
        ?int $limit,
        string $categoryKey,
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

        $ver = $this->cache->version('posts');
        $cacheKey = "posts:v{$ver}:{$lang}:cat_{$categoryKey}:limit_" . ($limit ?: 'none') . ":page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, function () use ($limit, $categoryKey, $page, $perPage, $request): array {
            $query = Post::with(['author:id,name', 'blogCategory:id,name,slug'])
                ->select(['id', 'title', 'slug', 'image', 'category', 'blog_category_id', 'author_id', 'is_published', 'is_trending', 'created_at', 'updated_at'])
                ->where('is_published', true)
                ->latest();

            if ($categoryKey !== 'all') {
                $query->where(function ($q) use ($categoryKey) {
                    $q->whereHas('blogCategory', function ($sub) use ($categoryKey) {
                        $sub->where('slug', $categoryKey);
                    })->orWhere('category', $categoryKey);
                });
            }

            if ($limit) {
                $posts = $query->take($limit)->get();

                return PostResource::collection($posts)->resolve($request);
            }

            $postsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => PostResource::collection($postsPaginated->items())->resolve($request),
                'current_page' => $postsPaginated->currentPage(),
                'last_page' => $postsPaginated->lastPage(),
                'total' => $postsPaginated->total(),
                'has_more' => $postsPaginated->hasMorePages(),
            ];
        });
    }

    public function listCategories(string $lang): array
    {
        $ver = $this->cache->version('posts');
        $cacheKey = "blog_categories:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, function () use ($lang): array {
            return BlogCategory::select(['id', 'name', 'slug'])
                ->get()
                ->map(static fn (BlogCategory $cat): array => [
                    'id' => strtoupper(trim($cat->slug)),
                    'label' => LocalizationHelper::trans($cat->name, $lang),
                ])
                ->toArray();
        });
    }

    public function listTrending(string $lang): array
    {
        $ver = $this->cache->version('posts');
        $cacheKey = "trending_posts:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, function () use ($lang): array {
            return Post::with('author:id,name')
                ->select(['id', 'title', 'slug', 'author_id', 'is_published', 'is_trending', 'created_at'])
                ->where('is_published', true)
                ->where('is_trending', true)
                ->latest()
                ->take(5)
                ->get()
                ->map(static fn (Post $post): array => [
                    'id' => $post->id,
                    'title' => LocalizationHelper::trans($post->title, $lang),
                    'slug' => $post->slug,
                    'author' => $post->author?->name ?? 'Admin',
                ])
                ->toArray();
        });
    }

    public function findPost(Request $request, string|int $idOrSlug, string $slugKey, string $lang): array
    {
        if ($slugKey === 'invalid') {
            abort(404);
        }

        $ver = $this->cache->version('posts');
        $cacheKey = "post_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $post = Post::with(['author:id,name', 'blogCategory:id,name,slug'])
                ->where('is_published', true)
                ->where(function ($query) use ($idOrSlug) {
                    $query->where('slug', $idOrSlug)
                        ->orWhere('id', $idOrSlug);
                })
                ->firstOrFail();

            $data = (new PostResource($post))->resolve($request);
            $this->cache->put($cacheKey, $data, 3600);
        }

        return $data;
    }
}
