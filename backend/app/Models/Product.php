<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['products'];
    }

    protected static function apiCacheKeys(): array
    {
        return ['product_categories_en', 'product_categories_ar'];
    }

    protected $fillable = [
        'slug',
        'name',
        'category',
        'title',
        'description',
        'image',
        'image_mobile',
        'demo_link',
        'order_link',
        'features',
        'tech_specs',
        'why_choose_us',
        'samples',
        'is_active',
    ];

    protected $casts = [
        'name' => 'json',
        'category' => 'json',
        'title' => 'json',
        'description' => 'json',
        'features' => 'json',
        'tech_specs' => 'json',
        'why_choose_us' => 'json',
        'samples' => 'json',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Paginated product caches (products_{lang}_cat_*) expire naturally in 1 hour.
        // Clear the category listing cache which is simpler and static.
        $clear = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('product_categories_en');
            \Illuminate\Support\Facades\Cache::forget('product_categories_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
