<?php

namespace Tests\Feature;

use App\Filament\Pages\AiGenerationDashboard;
use App\Filament\Pages\AiPageBuilder;
use App\Filament\Pages\ManageHomePage;
use App\Filament\Pages\ManageSeoSettings;
use App\Filament\Pages\ManageTranslations;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function read_only_user_cannot_access_admin_pages_in_user_or_admin_panel(): void
    {
        Role::firstOrCreate(['name' => 'read_only', 'guard_name' => 'web']);
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->assertFalse(ManageHomePage::canAccess());
        $this->assertFalse(ManageSeoSettings::canAccess());
        $this->assertFalse(ManageTranslations::canAccess());
        $this->assertFalse(AiGenerationDashboard::canAccess());
        $this->assertFalse(AiPageBuilder::canAccess());

        foreach (['/user/manage-home-page', '/user/ai-page-builder', '/admin/manage-home-page', '/admin/ai-page-builder'] as $path) {
            $response = $this->get($path);
            $this->assertContains($response->status(), [403, 404], "Expected 403 or 404 for {$path}, got {$response->status()}");
        }
    }

    #[Test]
    public function super_admin_can_access_admin_pages(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $this->actingAs($admin);

        $this->assertTrue(ManageHomePage::canAccess());
        $this->assertTrue(ManageSeoSettings::canAccess());
        $this->assertTrue(ManageTranslations::canAccess());
        $this->assertTrue(AiGenerationDashboard::canAccess());
        $this->assertTrue(AiPageBuilder::canAccess());
    }

    #[Test]
    public function read_only_user_gets_403_when_downloading_quotation_files(): void
    {
        \Illuminate\Support\Facades\Storage::fake('local');
        \Illuminate\Support\Facades\Storage::disk('local')->put('quotations/spec.webp', 'fake-image');

        $quotation = \App\Models\Quotation::create([
            'name' => 'Client A',
            'email' => 'clienta@example.com',
            'phone' => '01000000000',
            'files' => ['quotations/spec.webp'],
            'status' => 'pending',
        ]);

        $readOnlyUser = User::factory()->create();
        $this->actingAs($readOnlyUser)
            ->get(route('admin.quotation.file.download', ['quotation' => $quotation->id, 'filename' => 'spec.webp']))
            ->assertForbidden();

        $admin = User::factory()->create();
        $admin->assignRole('super_admin');
        $this->actingAs($admin)
            ->get(route('admin.quotation.file.download', ['quotation' => $quotation->id, 'filename' => 'spec.webp']))
            ->assertOk();
    }
}
