<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use App\Models\Service;
use Illuminate\Database\Seeder;

class LinkPricingPlansToServicesSeeder extends Seeder
{
    public function run(): void
    {
        $service = Service::first();
        if ($service) {
            PricingPlan::whereNull('service_id')->update(['service_id' => $service->id]);
        }
    }
}
