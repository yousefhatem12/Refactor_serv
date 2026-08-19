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

    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    protected static function booted()
    {
        // IMPORTANT: Do NOT use Cache::flush() here — it would wipe ALL application cache
        // (sessions, settings, services, etc.) whenever any pricing plan is edited.
        // Instead, clear only the relevant pricing plan cache keys.
        $clear = function (self $plan): void {
            $serviceId = $plan->service_id;
            foreach (['en', 'ar'] as $lang) {
                \Illuminate\Support\Facades\Cache::forget("pricing_plans_{$lang}_service_all");
                \Illuminate\Support\Facades\Cache::forget("pricing_plans_{$lang}_service_{$serviceId}");
            }
        };
        static::saved($clear);
        static::deleted($clear);

        static::saving(function ($model) {
            if (empty($model->price)) {
                $model->price = '0';
            }
            if (empty($model->currency)) {
                $model->currency = '$';
            }
        });
    }
}
