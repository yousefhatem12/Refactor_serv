<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationItem extends Model
{
    protected $fillable = [
        'request_id',
        'keyword',
        'status',
        'post_id',
        'error_message',
        'parsed_response',
        'quality_report',
        'usage_metadata',
        'attempts',
    ];

    protected $casts = [
        'parsed_response' => 'json',
        'quality_report' => 'json',
        'usage_metadata' => 'json',
        'attempts' => 'integer',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(AiGenerationRequest::class, 'request_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
