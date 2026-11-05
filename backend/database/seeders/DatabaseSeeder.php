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
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'read_only', 'guard_name' => 'web']);

        $adminEmail = env('ADMIN_EMAIL', 'admin@serv5.com');
        $adminPassword = env('ADMIN_PASSWORD', 'admin123456');

        $user = User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Super Admin',
                'password' => bcrypt($adminPassword),
            ]
        );
        $user->syncRoles([$role]);

        $serv5Admin = User::updateOrCreate(
            ['email' => 'admin@serv5.com'],
            [
                'name' => 'Serv5 Admin',
                'password' => bcrypt('admin123456'),
            ]
        );
        $serv5Admin->syncRoles([$role]);

        $this->call([
            InitialContentSeeder::class,
            FooterSeeder::class,
            ProductsAndCareersSeeder::class,
            ProjectSeeder::class,
            BlogSeeder::class,
            TestimonialSeeder::class,
            ServiceSeeder::class,
            LinkPricingPlansToServicesSeeder::class,
            FaqSeeder::class,
            HomeFeaturesSeeder::class,
        ]);
    }
}
