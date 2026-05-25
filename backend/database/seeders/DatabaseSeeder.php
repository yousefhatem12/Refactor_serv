<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->assignRole($role);

        $this->call([
            InitialContentSeeder::class,
            FooterSeeder::class,
            ProductsAndCareersSeeder::class,
            ProjectSeeder::class,
            ServiceSeeder::class,
            LinkPricingPlansToServicesSeeder::class,
        ]);
    }
}
