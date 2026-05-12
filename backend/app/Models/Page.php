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
    ];

    protected $casts = [
        'title' => 'json',
        'subtitle' => 'json',
        'content' => 'json',
        'seo_title' => 'json',
        'seo_description' => 'json',
        'sections' => 'array',
    ];
}
