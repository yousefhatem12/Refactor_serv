<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory, ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['bank_accounts'];
    }

    protected $fillable = [
        'name',
        'owner',
        'account',
        'image',
        'type',
        'country',
        'brand_color',
        'shadow',
        'sort_order',
    ];

    protected $casts = [
        'name' => 'json',
        'owner' => 'json',
        'account' => 'json',
    ];

    protected static function booted()
    {
        // IMPORTANT: Do NOT use Cache::flush() — it wipes ALL application cache.
        // Since bank_accounts cache keys include dynamic country/page params, we clear
        // them with a prefix match on the cache table (safe for database cache driver).
        // If using file/redis cache, keys expire naturally after 1 hour (TTL=3600).
        $clear = static function (): void {
            $prefix = config('cache.prefix') . 'bank_accounts_';
            try {
                \Illuminate\Support\Facades\DB::table('cache')
                    ->where('key', 'like', $prefix . '%')
                    ->delete();
            } catch (\Throwable) {
                // Non-database cache driver: keys will expire naturally via TTL.
            }
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
