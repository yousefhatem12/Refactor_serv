<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['testimonials'];
    }
    protected $fillable = ['client_name', 'position', 'review', 'image', 'is_active', 'rating', 'video_url', 'audio_url'];

    protected $casts = [
        'client_name' => 'json',
        'position' => 'json',
        'review' => 'json',
    ];

    protected static function booted()
    {
        $clear = function () {
            \Illuminate\Support\Facades\Cache::forget('testimonials_en');
            \Illuminate\Support\Facades\Cache::forget('testimonials_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
