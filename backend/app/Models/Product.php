<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'category',
        'title',
        'description',
        'image',
        'demo_link',
        'order_link',
        'features',
        'tech_specs',
        'why_choose_us',
        'samples',
        'is_active',
    ];

    protected $casts = [
        'name' => 'json',
        'category' => 'json',
        'title' => 'json',
        'description' => 'json',
        'features' => 'json',
        'tech_specs' => 'json',
        'why_choose_us' => 'json',
        'samples' => 'json',
        'is_active' => 'boolean',
    ];
}
