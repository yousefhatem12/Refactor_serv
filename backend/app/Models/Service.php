<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'description',
        'icon',
        'is_active',
        'sections',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'name' => 'json',
        'description' => 'json',
        'sections' => 'json',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->name);
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('services_hierarchy_ar');
            \Illuminate\Support\Facades\Cache::forget('services_hierarchy_en');
            \Illuminate\Support\Facades\Cache::forget('services_ar');
            \Illuminate\Support\Facades\Cache::forget('services_en');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('services_hierarchy_ar');
            \Illuminate\Support\Facades\Cache::forget('services_hierarchy_en');
            \Illuminate\Support\Facades\Cache::forget('services_ar');
            \Illuminate\Support\Facades\Cache::forget('services_en');
        });
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class, 'parent_id');
    }

    public function pricingPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PricingPlan::class);
    }
}
