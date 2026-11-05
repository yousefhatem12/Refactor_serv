<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    use ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['footer'];
    }
    protected $fillable = ['label', 'url', 'column_name', 'sort_order'];

    protected $casts = [
        'label' => 'json',
    ];

    protected static function booted(): void
    {
        $clear = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('footer_data_en');
            \Illuminate\Support\Facades\Cache::forget('footer_data_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
