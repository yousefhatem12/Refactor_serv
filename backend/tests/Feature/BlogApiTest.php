<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_published_posts()
    {
        $author = User::factory()->create();
        
        Post::create([
            'title' => ['en' => 'Test Post Title', 'ar' => 'عنوان التجربة'],
            'content' => ['en' => 'Test Content', 'ar' => 'محتوى التجربة'],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'test-post-title',
        ]);

        $response = $this->getJson('/api/v1/blog?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Test Post Title',
                'slug' => 'test-post-title',
            ]);
    }

    /** @test */
    public function it_does_not_list_unpublished_posts()
    {
        $author = User::factory()->create();

        Post::create([
            'title' => ['en' => 'Unpublished Post', 'ar' => 'غير منشور'],
            'content' => ['en' => 'Content', 'ar' => 'محتوى'],
            'is_published' => false,
            'author_id' => $author->id,
            'slug' => 'unpublished-post',
        ]);

        $response = $this->getJson('/api/v1/blog?lang=en');

        $response->assertStatus(200);
        $this->assertEmpty($response->json());
    }

    /** @test */
    public function it_can_list_categories()
    {
        BlogCategory::create([
            'name' => ['en' => 'Tech', 'ar' => 'تكنولوجيا'],
            'slug' => 'tech',
        ]);

        $response = $this->getJson('/api/v1/blog/categories?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => 'TECH',
                'label' => 'Tech',
            ]);
    }

    /** @test */
    public function it_can_list_trending_posts()
    {
        $author = User::factory()->create();

        Post::create([
            'title' => ['en' => 'Trending Title', 'ar' => 'شائع'],
            'content' => ['en' => 'Content', 'ar' => 'محتوى'],
            'is_published' => true,
            'is_trending' => true,
            'author_id' => $author->id,
            'slug' => 'trending-title',
        ]);

        $response = $this->getJson('/api/v1/blog/trending?lang=en');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Trending Title',
                'slug' => 'trending-title',
            ]);
    }

    /** @test */
    public function it_can_show_a_single_post()
    {
        $author = User::factory()->create();

        $post = Post::create([
            'title' => ['en' => 'Single Post', 'ar' => 'منشور واحد'],
            'content' => ['en' => 'Content', 'ar' => 'محتوى'],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'single-post',
        ]);

        $response = $this->getJson('/api/v1/blog/single-post?lang=en');

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Single Post')
            ->assertJsonPath('data.slug', 'single-post');
    }
}
