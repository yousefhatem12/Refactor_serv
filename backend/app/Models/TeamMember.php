<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['team'];
    }
    protected $fillable = ['name', 'role', 'bio', 'image', 'facebook_url', 'linkedin_url', 'is_active'];

    protected $casts = [
        'name' => 'json',
        'role' => 'json',
        'bio' => 'json',
    ];

    protected static function booted()
    {
        $clear = function () {
            \Illuminate\Support\Facades\Cache::forget('team_members_en');
            \Illuminate\Support\Facades\Cache::forget('team_members_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
