<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PageApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_fetch_a_page_by_slug_in_english()
    {
        Page::create([
            'slug' => 'about-us',
            'title' => json_encode(['en' => 'About Us English', 'ar' => 'من نحن']),
            'subtitle' => json_encode(['en' => 'Subtitle EN', 'ar' => 'Subtitle AR']),
            'content' => json_encode(['en' => 'Content EN', 'ar' => 'Content AR']),
            'sections' => [
                'en' => [
                    ['type' => 'journey', 'data' => ['title' => 'Our Journey']]
                ],
                'ar' => [
                    ['type' => 'journey', 'data' => ['title' => 'رحلتنا']]
                ]
            ]
        ]);

        $response = $this->getJson('/api/v1/pages/about-us?lang=en');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'slug' => 'about-us',
                    'title' => 'About Us English',
                    'sections' => [
                        'blocks' => [
                            ['type' => 'journey', 'data' => ['title' => 'Our Journey']]
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function it_can_fetch_a_page_by_slug_in_arabic()
    {
        Page::create([
            'slug' => 'about-us',
            'title' => ['en' => 'About Us English', 'ar' => 'من نحن'],
            'subtitle' => ['en' => 'Subtitle EN', 'ar' => 'Subtitle AR'],
            'content' => ['en' => 'Content EN', 'ar' => 'Content AR'],
            'sections' => [
                'en' => [
                    ['type' => 'journey', 'data' => ['title' => 'Our Journey']]
                ],
                'ar' => [
                    ['type' => 'journey', 'data' => ['title' => 'رحلتنا']]
                ]
            ]
        ]);

        $response = $this->getJson('/api/v1/pages/about-us?lang=ar');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'slug' => 'about-us',
                    'title' => 'من نحن',
                    'sections' => [
                        'blocks' => [
                            ['type' => 'journey', 'data' => ['title' => 'رحلتنا']]
                        ]
                    ]
                ]
            ]);
    }

    #[Test]
    public function it_returns_404_if_page_does_not_exist()
    {
        $response = $this->getJson('/api/v1/pages/non-existent');
        $response->assertStatus(404);
    }

    #[Test]
    public function it_clears_cache_automatically_when_page_is_saved()
    {
        $page = Page::create([
            'slug' => 'about-us',
            'title' => ['en' => 'Title 1', 'ar' => 'العنوان 1'],
            'sections' => []
        ]);

        // First request to populate the cache
        $response1 = $this->getJson('/api/v1/pages/about-us?lang=en');
        $response1->assertJsonPath('data.title', 'Title 1');

        // Assert cache has been populated
        $this->assertTrue(Cache::has('page_about-us_en'));

        // Update the title
        $page->title = ['en' => 'Title 2', 'ar' => 'العنوان 2'];
        $page->save();

        // Second request to check if cache was cleared and returns updated data
        $response2 = $this->getJson('/api/v1/pages/about-us?lang=en');
        $response2->assertJsonPath('data.title', 'Title 2');
    }
}
