<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
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
        // Clear services API cache on any change.
        $clear = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('services_v2_en');
            \Illuminate\Support\Facades\Cache::forget('services_v2_ar');
        };
        static::saved($clear);
        static::deleted($clear);
    }

    public function pricingPlans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PricingPlan::class);
    }
}
