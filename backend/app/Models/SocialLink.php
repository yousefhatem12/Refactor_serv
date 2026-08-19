<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    protected $fillable = ['platform', 'url', 'is_active'];

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
