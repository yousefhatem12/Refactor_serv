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
        $page = $request->query('page');
        $perPage = $request->query('per_page', 9);

        $cacheKey = "products_{$lang}_cat_" . ($category ?: 'all') . "_page_" . ($page ?: 'none') . "_per_" . $perPage;

        $response = Cache::remember($cacheKey, 3600, function () use ($lang, $page, $perPage, $category) {
            $query = Product::where('is_active', true)->orderBy('id', 'asc');

            if ($category && strtolower($category) !== 'all') {
                $query->where(function ($q) use ($category) {
                    $q->where('category->en', $category)
                      ->orWhere('category->ar', $category)
                      ->orWhere('category', $category);
                });
            }

            if ($page) {
                $productsPaginated = $query->paginate($perPage);
                return [
                    'type' => 'paginated',
                    'data' => [
                        'data' => ProductResource::collection($productsPaginated->items())->resolve(),
                        'current_page' => $productsPaginated->currentPage(),
                        'last_page' => $productsPaginated->lastPage(),
                        'has_more' => $productsPaginated->hasMorePages(),
                    ]
                ];
            }

            $products = $query->get();
            return [
                'type' => 'flat',
                'data' => ProductResource::collection($products)->resolve()
            ];
        });

        return response()->json($response['data']);
    }

    public function categories()
    {
        $lang = $this->getLang();
        $cacheKey = "product_categories_{$lang}";

        $categories = Cache::remember($cacheKey, 3600, function () use ($lang) {
            $products = Product::where('is_active', true)->get();
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

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return new ProductResource($product);
    }
}
