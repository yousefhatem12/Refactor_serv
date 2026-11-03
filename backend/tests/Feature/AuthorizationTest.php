<?php

namespace Tests\Feature;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_access_admin_panel()
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }

    #[Test]
    public function users_without_super_admin_role_cannot_access_admin_panel()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/admin');

        $this->assertTrue($response->status() === 403 || $response->isRedirect());
    }

    #[Test]
    public function super_admins_can_access_admin_panel()
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $response = $this->actingAs($user)
            ->get('/admin');

        $response->assertSuccessful();
    }
}
