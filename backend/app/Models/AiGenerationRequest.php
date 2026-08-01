<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiGenerationRequest extends Model
{
    protected $fillable = [
        'created_by',
        'provider',
        'model',
        'generation_mode',
        'idempotency_key',
        'request_hash',
        'settings_snapshot',
        'main_keyword',
        'secondary_keywords',
        'language',
        'article_count',
        'category_id',
        'page_id',
        'author_id',
        'status',
        'total_items',
        'completed_items',
        'failed_items',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'secondary_keywords' => 'json',
        'settings_snapshot' => 'json',
        'total_items' => 'integer',
        'completed_items' => 'integer',
        'failed_items' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AiGenerationItem::class, 'request_id');
    }
}
