<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;

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
    }
}
