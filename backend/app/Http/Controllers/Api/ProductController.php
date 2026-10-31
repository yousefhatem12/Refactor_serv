<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends BaseApiController
{
    public function index(Request $request)
    {
        $lang = $this->getLang();
        $category = $request->query('category');
        $categoryKey = $this->sanitizeSlugParam($category);
        $page = $this->pageNumber($request, 1);
        $perPage = $this->perPage($request, 12);
        $ver = $this->cacheVersion('products');

        if ($category && strtolower((string) $category) !== 'all' && $categoryKey === 'invalid' && mb_strlen((string) $category) > 64) {
            return response()->json([
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ]);
        }

        $safeCatHash = $category && strtolower((string) $category) !== 'all' ? md5(strtolower(trim((string) $category))) : 'all';
        $cacheKey = "products:v{$ver}:{$lang}:cat_{$safeCatHash}:page_{$page}:per_{$perPage}";

        $response = Cache::remember($cacheKey, 3600, function () use ($page, $perPage, $category, $request) {
            $query = Product::where('is_active', true)
                ->select(['id', 'slug', 'name', 'category', 'title', 'description', 'image', 'image_mobile', 'demo_link', 'order_link', 'tech_specs', 'is_active'])
                ->orderBy('id', 'asc');

            if ($category && strtolower((string) $category) !== 'all') {
                $query->where(function ($q) use ($category) {
                    $q->where('category->en', $category)
                      ->orWhere('category->ar', $category)
                      ->orWhere('category', $category);
                });
            }

            $productsPaginated = $query->paginate($perPage, ['*'], 'page', $page);

            return [
                'data' => ProductResource::collection($productsPaginated->items())->resolve($request),
                'current_page' => $productsPaginated->currentPage(),
                'last_page' => $productsPaginated->lastPage(),
                'total' => $productsPaginated->total(),
                'has_more' => $productsPaginated->hasMorePages(),
            ];
        });

        return response()->json($response);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('products');
        $cacheKey = "product_categories:v{$ver}:{$lang}";

        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            $products = Product::where('is_active', true)
                ->whereNotNull('category')
                ->select(['category'])
                ->get();
            $cats = $products->map(function ($p) use ($lang) {
                return $this->trans($p->category, $lang);
            })->filter()->unique()->values();

            return $cats->map(function ($cat) {
                return [
                    'id' => $cat,
                    'label' => $cat,
                ];
            })->toArray();
        });

        return response()->json($categories);
    }

    public function show(Request $request, string $slug)
    {
        $lang = $this->getLang();
        $ver = $this->cacheVersion('products');
        $slugKey = $this->sanitizeSlugParam($slug);

        if ($slugKey === 'invalid') {
            abort(404);
        }

        $cacheKey = "product_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = Cache::get($cacheKey);
        if ($data === null) {
            $product = Product::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug)
                          ->orWhere('id', $slug);
                })
                ->firstOrFail();

            $data = (new ProductResource($product))->resolve($request);
            Cache::put($cacheKey, $data, 3600);
        }

        return response()->json(['data' => $data]);
    }
}
