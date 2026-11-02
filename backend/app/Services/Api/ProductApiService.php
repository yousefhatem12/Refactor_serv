<?php

namespace App\Services\Api;

use App\Contracts\Api\ApiCacheServiceInterface;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Support\LocalizationHelper;
use Illuminate\Http\Request;

class ProductApiService
{
    public function __construct(
        private readonly ApiCacheServiceInterface $cache
    ) {}

    public function listProducts(
        Request $request,
        string $lang,
        mixed $category,
        string $categoryKey,
        int $page,
        int $perPage
    ): array {
        if ($category && strtolower((string) $category) !== 'all' && $categoryKey === 'invalid' && mb_strlen((string) $category) > 64) {
            return [
                'data' => [],
                'current_page' => $page,
                'last_page' => 1,
                'total' => 0,
                'has_more' => false,
            ];
        }

        $ver = $this->cache->version('products');
        $safeCatHash = $category && strtolower((string) $category) !== 'all' ? md5(strtolower(trim((string) $category))) : 'all';
        $cacheKey = "products:v{$ver}:{$lang}:cat_{$safeCatHash}:page_{$page}:per_{$perPage}";

        return $this->cache->remember($cacheKey, function () use ($page, $perPage, $category, $request): array {
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
    }

    public function listCategories(string $lang): array
    {
        $ver = $this->cache->version('products');
        $cacheKey = "product_categories:v{$ver}:{$lang}";

        return $this->cache->remember($cacheKey, function () use ($lang): array {
            $products = Product::where('is_active', true)
                ->whereNotNull('category')
                ->select(['category'])
                ->get();

            $cats = $products
                ->map(static fn (Product $p): string => LocalizationHelper::trans($p->category, $lang))
                ->filter()
                ->unique()
                ->values();

            return $cats
                ->map(static fn (string $cat): array => [
                    'id' => $cat,
                    'label' => $cat,
                ])
                ->toArray();
        });
    }

    public function findProduct(Request $request, string $slug, string $slugKey, string $lang): array
    {
        if ($slugKey === 'invalid') {
            abort(404);
        }

        $ver = $this->cache->version('products');
        $cacheKey = "product_detail:v{$ver}:{$lang}:{$slugKey}";

        $data = $this->cache->get($cacheKey);
        if ($data === null) {
            $product = Product::where('is_active', true)
                ->where(function ($query) use ($slug) {
                    $query->where('slug', $slug)
                        ->orWhere('id', $slug);
                })
                ->firstOrFail();

            $data = (new ProductResource($product))->resolve($request);
            $this->cache->put($cacheKey, $data, 3600);
        }

        return $data;
    }
}
