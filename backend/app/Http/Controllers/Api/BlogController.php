<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\BlogCategory;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BlogController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $limit = $this->limit($request->query('limit'));
        $categoryRaw = $request->query('category_raw') ?? $request->query('category');
        $categoryKey = $this->sanitizeSlugParam($categoryRaw);
        $page = $this->pageNumber($request, 1);
        $perPage = $this->perPage($request, 12);
        $ver = $this->cacheVersion('posts');

        if ($categoryKey === 'invalid') {
            return response()->json([
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ]);
        }

        $cacheKey = "posts:v{$ver}:{$lang}:cat_{$categoryKey}:limit_" . ($limit ?: 'none') . ":page_{$page}:per_{$perPage}";

        $response = Cache::remember($cacheKey, 3600, function () use ($limit, $categoryKey, $page, $perPage, $request) {
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
                $posts = $query->take((int) $limit)->get();
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

        return response()->json($response);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('posts');
        $cacheKey = "blog_categories:v{$ver}:{$lang}";
        
        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return BlogCategory::select(['id', 'name', 'slug'])->get()->map(function ($cat) use ($lang) {
                return [
                    'id' => strtoupper(trim($cat->slug)),
                    'label' => $this->trans($cat->name, $lang),
                ];
            })->toArray();
        });
        
        return response()->json($categories);
    }

    public function trending()
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('posts');
        $cacheKey = "trending_posts:v{$ver}:{$lang}";

        $posts = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Post::with('author:id,name')
                ->select(['id', 'title', 'slug', 'author_id', 'is_published', 'is_trending', 'created_at'])
                ->where('is_published', true)
                ->where('is_trending', true)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($post) use ($lang) {
                    return [
                        'id' => $post->id,
                        'title' => $this->trans($post->title, $lang),
                        'slug' => $post->slug,
                        'author' => $post->author?->name ?? 'Admin',
                    ];
                })->toArray();
        });
        return response()->json($posts);
    }

    public function show(Request $request, string|int $idOrSlug)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('posts');
        $slugKey = $this->sanitizeSlugParam($idOrSlug);

        if ($slugKey === 'invalid') {
            abort(404);
        }

        $cacheKey = "post_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = Cache::get($cacheKey);
        if ($data === null) {
            $post = Post::with(['author:id,name', 'blogCategory:id,name,slug'])
                ->where('is_published', true)
                ->where(function ($query) use ($idOrSlug) {
                    $query->where('slug', $idOrSlug)
                          ->orWhere('id', $idOrSlug);
                })
                ->firstOrFail();

            $data = (new PostResource($post))->resolve($request);
            Cache::put($cacheKey, $data, 3600);
        }

        return response()->json(['data' => $data]);
    }
}
