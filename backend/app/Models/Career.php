<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Career extends Model
{
    use HasFactory, ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['careers'];
    }

    protected $fillable = [
        'title',
        'department',
        'location',
        'salary',
        'description',
        'responsibilities',
        'requirements',
        'is_active',
    ];

    protected $casts = [
        'title' => 'json',
        'location' => 'json',
        'salary' => 'json',
        'description' => 'json',
        'responsibilities' => 'json',
        'requirements' => 'json',
        'is_active' => 'boolean',
    ];

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }
}
