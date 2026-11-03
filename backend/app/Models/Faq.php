<?php

namespace App\Models;

use App\Traits\ClearsApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory, ClearsApiCache;

    protected static function apiCacheScopes(): array
    {
        return ['faqs'];
    }

    protected $fillable = [
        'question',
        'answer',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'question' => 'json',
        'answer' => 'json',
    ];
}
