<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;



    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function a_user_can_submit_a_project_consultation()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super_admin');

        $response = $this->postJson('/api/v1/consultations', [
            'project_type' => 'website',
            'has_docs' => 'yes',
            'budget' => '12to35',
            'timeline' => '1month',
            'decision_maker' => 'me',
            'references' => 'http://example.com',
            'name' => 'Consultation seeker',
            'phone' => '0555555555',
            'email' => 'consult@example.com',
            'company' => 'My Startup',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Consultation request submitted successfully');

        $this->assertDatabaseHas('project_consultations', [
            'project_type' => 'website',
            'name' => 'Consultation seeker',
            'email' => 'consult@example.com',
            'company' => 'My Startup',
        ]);
    }

    #[Test]
    public function project_consultation_submission_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/consultations', [
            'name' => '',
            'phone' => '',
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email']);
    }
}
