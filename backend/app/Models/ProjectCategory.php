<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name'];

    protected $casts = [
        'name' => 'json',
    ];

    protected static function booted(): void
    {
        $clear = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('project_categories_en');
            \Illuminate\Support\Facades\Cache::forget('project_categories_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
