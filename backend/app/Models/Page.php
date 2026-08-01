<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'hero_image',
        'content',
        'seo_title',
        'seo_description',
        'sections',
        'is_published',
        'ai_generated',
        'ai_status',
        'ai_metadata',
    ];

    protected $casts = [
        'title' => 'json',
        'subtitle' => 'json',
        'content' => 'json',
        'seo_title' => 'json',
        'seo_description' => 'json',
        'sections' => 'array',
        'is_published' => 'boolean',
        'ai_generated' => 'boolean',
        'ai_metadata' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($page) {
            \Illuminate\Support\Facades\Cache::forget("page_{$page->slug}_en");
            \Illuminate\Support\Facades\Cache::forget("page_{$page->slug}_ar");
        });
    }
}
