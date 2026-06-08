<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;



    /** @test */
    public function a_user_can_submit_a_contact_message()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->postJson('/api/v1/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '1234567890',
            'message' => 'Hello Serv5!',
            'type' => 'inquiry',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Message sent successfully');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello Serv5!',
        ]);
    }

    /** @test */
    public function contact_message_submission_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function a_user_can_submit_a_quotation_request()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $file = UploadedFile::fake()->image('mock-project.png');

        $response = $this->postJson('/api/v1/quotations', [
            'name' => 'John Doe',
            'company' => 'Google',
            'phone' => '0123456789',
            'email' => 'john@example.com',
            'files' => [$file],
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Quotation request submitted successfully');

        $this->assertDatabaseHas('quotations', [
            'name' => 'John Doe',
            'company' => 'Google',
            'email' => 'john@example.com',
        ]);
    }
}
