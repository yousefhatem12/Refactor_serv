<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'price',
        'currency',
        'period',
        'features',
        'is_popular',
        'is_active',
        'button_text',
        'button_url',
        'sort_order',
    ];

    protected $casts = [
        'service_id' => 'integer',
        'name' => 'array',
        'period' => 'array',
        'features' => 'array',
        'button_text' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
