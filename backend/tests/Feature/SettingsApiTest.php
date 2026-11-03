<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\BankAccount;
use App\Models\Feature;
use App\Models\FooterLink;
use App\Models\NavLink;
use App\Models\PricingPlan;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_get_settings()
    {
        Setting::create([
            'key' => 'site_name',
            'value' => json_encode(['en' => 'Serv5 English', 'ar' => 'سيرف فايف']),
            'type' => 'text',
        ]);

        $response = $this->getJson('/api/v1/settings?lang=en');

        $response->assertStatus(200)
            ->assertJson([
                'site_name' => 'Serv5 English',
            ]);
    }

    #[Test]
    public function it_can_get_testimonials()
    {
        Testimonial::create([
            'client_name' => json_encode(['en' => 'John Doe', 'ar' => 'جون دو']),
            'position' => json_encode(['en' => 'Manager', 'ar' => 'مدير']),
            'review' => json_encode(['en' => 'Great work!', 'ar' => 'عمل رائع!']),
            'is_active' => true,
            'rating' => 5,
        ]);

        $response = $this->getJson('/api/v1/testimonials?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'client_name' => 'John Doe',
                'review' => 'Great work!',
            ]);
    }

    #[Test]
    public function it_can_get_team_members()
    {
        TeamMember::create([
            'name' => json_encode(['en' => 'Alice', 'ar' => 'أليس']),
            'role' => json_encode(['en' => 'Developer', 'ar' => 'مطور']),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/team?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Alice',
                'role' => 'Developer',
            ]);
    }

    #[Test]
    public function it_can_get_features()
    {
        Feature::create([
            'title' => json_encode(['en' => 'Fast Development', 'ar' => 'تطوير سريع']),
            'description' => json_encode(['en' => 'Very fast', 'ar' => 'سريع جداً']),
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/features?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Fast Development',
                'description' => 'Very fast',
            ]);
    }

    #[Test]
    public function it_can_get_footer_data()
    {
        Setting::create(['key' => 'footer_heading_en', 'value' => 'Good design', 'type' => 'text']);
        Setting::create(['key' => 'footer_copyright_en', 'value' => '© 2026', 'type' => 'text']);

        FooterLink::create([
            'label' => ['en' => 'Home', 'ar' => 'الرئيسية'],
            'url' => '/',
            'column_name' => 'useful_links',
            'sort_order' => 1,
        ]);

        SocialLink::create([
            'platform' => 'facebook',
            'url' => 'https://facebook.com',
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/footer-data?lang=en');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'texts' => ['heading', 'description', 'copyright'],
                'links',
                'socials',
            ]);
    }

    #[Test]
    public function it_can_get_nav_links()
    {
        NavLink::create([
            'label' => ['en' => 'Home', 'ar' => 'الرئيسية'],
            'url' => '/',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/nav-links?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'href' => '/',
                'name' => 'Home',
            ]);
    }

    #[Test]
    public function it_can_get_pricing_plans()
    {
        PricingPlan::create([
            'name' => ['en' => 'Basic', 'ar' => 'الأساسية'],
            'price' => '100',
            'currency' => 'USD',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/pricing-plans?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'Basic',
                'price' => '100',
            ]);
    }

    #[Test]
    public function it_can_get_bank_accounts()
    {
        BankAccount::create([
            'name' => ['en' => 'HSBC', 'ar' => 'إتش إس بي سي'],
            'owner' => ['en' => 'Serv5', 'ar' => 'سيرف فايف'],
            'account' => ['en' => '1234567890', 'ar' => '1234567890'],
            'country' => 'Egypt',
            'sort_order' => 1,
        ]);

        $response = $this->getJson('/api/v1/bank-accounts?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => [
                    'en' => 'HSBC',
                    'ar' => 'إتش إس بي سي',
                ],
                'account' => [
                    'en' => '1234567890',
                    'ar' => '1234567890',
                ],
            ]);
    }
}
