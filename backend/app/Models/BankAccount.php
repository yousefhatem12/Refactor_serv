<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

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
        $clear = function () {
            // Since cache keys for bank accounts include country/page/per_page, flush cache to be safe
            \Illuminate\Support\Facades\Cache::flush();
        };
        static::saved($clear);
        static::deleted($clear);
    }
}
