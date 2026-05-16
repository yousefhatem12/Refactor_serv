<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'category',
        'description',
        'image',
        'client',
        'url',
        'tags',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'tags' => 'json',
    ];
}
