<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['features'];
    }
    protected $fillable = ['title', 'description', 'icon', 'is_active'];

    protected $casts = [
        'title' => 'json',
        'description' => 'json',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        $clear = function () {
            \Illuminate\Support\Facades\Cache::forget('features_en');
            \Illuminate\Support\Facades\Cache::forget('features_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
