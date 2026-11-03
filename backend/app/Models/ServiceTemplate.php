<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ServiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'hero_style',
        'default_hero_image',
        'description',
        'blocks_preset',
    ];

    protected $casts = [
        'blocks_preset' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($template) {
            if (empty($template->slug)) {
                $template->slug = Str::slug($template->name);
            }
        });
    }
}
