<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'category',
        'blog_category_id',
        'is_published',
        'is_trending',
        'author_id',
        'seo_title',
        'seo_description',
        'ai_generated',
        'ai_status',
        'ai_metadata',
        'image_metadata',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_trending' => 'boolean',
        'title' => 'json',
        'content' => 'json',
        'seo_title' => 'json',
        'seo_description' => 'json',
        'ai_generated' => 'boolean',
        'ai_metadata' => 'json',
        'image_metadata' => 'json',
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function blogCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $title = is_array($post->title) ? ($post->title['en'] ?? '') : $post->title;
                $post->slug = Str::slug($title);
            }
        });
    }
}
