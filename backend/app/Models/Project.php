<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'slug',
        'name',
        'category',
        'description',
        'image',
        'client',
        'url',
        'tags',
        'is_featured',
    ];

    protected $casts = [
        'name' => 'json',
        'description' => 'json',
        'tags' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($project) {
            if (empty($project->slug)) {
                $name = is_array($project->name) ? ($project->name['en'] ?? '') : $project->name;
                $project->slug = \Illuminate\Support\Str::slug($name ?: 'project');
            }
        });
        static::updating(function ($project) {
            if (empty($project->slug)) {
                $name = is_array($project->name) ? ($project->name['en'] ?? '') : $project->name;
                $project->slug = \Illuminate\Support\Str::slug($name ?: 'project');
            }
        });
    }
}
