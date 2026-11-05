<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory, ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['posts'];
    }

    protected $fillable = ['slug', 'name'];

    protected $casts = [
        'name' => 'json',
    ];

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Post::class, 'blog_category_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $name = is_array($category->name) ? ($category->name['en'] ?? '') : $category->name;
                $category->slug = Str::slug($name ?: 'category');
            }
        });
        static::updating(function ($category) {
            if (empty($category->slug)) {
                $name = is_array($category->name) ? ($category->name['en'] ?? '') : $category->name;
                $category->slug = Str::slug($name ?: 'category');
            }
        });
        // Clear blog categories API cache on any change.
        $clear = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('blog_categories_en');
            \Illuminate\Support\Facades\Cache::forget('blog_categories_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
