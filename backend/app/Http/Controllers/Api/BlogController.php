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
        $categoryRaw = $request->query('category_raw');
        $page = $this->pageNumber($request);
        $perPage = $this->perPage($request, 9);

        $cacheKey = "posts_{$lang}_cat_" . ($categoryRaw ?: 'all') . "_limit_" . ($limit ?: 'none') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = Cache::remember($cacheKey, 3600, function () use ($lang, $limit, $categoryRaw, $page, $perPage) {
            $query = Post::with(['author', 'blogCategory'])
                ->where('is_published', true)
                ->latest();

            if ($categoryRaw && strtoupper($categoryRaw) !== 'ALL') {
                $query->where(function ($q) use ($categoryRaw) {
                    $q->whereHas('blogCategory', function ($sub) use ($categoryRaw) {
                        $sub->where('slug', strtolower($categoryRaw));
                    })->orWhere('category', strtolower($categoryRaw));
                });
            }

            if ($limit) {
                $posts = $query->take((int) $limit)->get();
                return [
                    'type' => 'flat',
                    'data' => PostResource::collection($posts)->resolve()
                ];
            }

            if ($page) {
                $postsPaginated = $query->paginate($perPage);
                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => PostResource::collection($postsPaginated->items())->resolve(),
                        'current_page' => $postsPaginated->currentPage(),
                        'last_page' => $postsPaginated->lastPage(),
                        'has_more' => $postsPaginated->hasMorePages(),
                    ]
                ];
            }

            $posts = $query->get();
            return [
                'type' => 'flat',
                'data' => PostResource::collection($posts)->resolve()
            ];
        });

        return response()->json($response['data']);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $cacheKey = "blog_categories_{$lang}";
        
        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return BlogCategory::all()->map(function ($cat) use ($lang) {
                return [
                    'id' => strtoupper(trim($cat->slug)),
                    'label' => $this->trans($cat->name, $lang),
                ];
            });
        });
        
        return response()->json($categories);
    }

    public function trending()
    {
        $lang = $this->getLang();
        $cacheKey = "trending_posts_{$lang}";

        $posts = Cache::remember($cacheKey, 3600, function () use ($lang) {
            return Post::with('author')
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
                });
        });
        return response()->json($posts);
    }

    public function show(string|int $idOrSlug)
    {
        $post = Post::with(['author', 'blogCategory'])
            ->where('is_published', true)
            ->where(function ($query) use ($idOrSlug) {
                $query->where('slug', $idOrSlug)
                      ->orWhere('id', $idOrSlug);
            })
            ->firstOrFail();

        return new PostResource($post);
    }
}
