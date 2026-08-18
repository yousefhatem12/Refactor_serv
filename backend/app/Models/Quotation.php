<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'company',
        'phone',
        'email',
        'country',
        'city',
        'request_type',
        'budget',
        'website_link',
        'content_managers',
        'pre_designed_logo',
        'design_images',
        'vision_goals',
        'product_brief',
        'color_preferences',
        'reference_sites',
        'additional_notes',
        'files',
        'status',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
