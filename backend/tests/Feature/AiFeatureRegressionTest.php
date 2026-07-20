<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\BlogCategory;
use App\Models\User;
use App\Models\AiCredential;
use App\Services\Ai\GeneratedPostService;
use App\Services\Ai\GenerationRequestNormalizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

class AiFeatureRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected User $author;
    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load AI migrations during test setup
        $this->artisan('migrate', ['--path' => 'database/migrations/ai']);

        // Seed basic dependencies
        $this->author = User::factory()->create();
        $this->category = BlogCategory::create([
            'name' => ['en' => 'Technology', 'ar' => 'تكنولوجيا'],
            'slug' => 'technology',
        ]);
    }

    /** @test */
    public function existing_published_posts_still_appear_in_api()
    {
        $post = Post::create([
            'title' => ['en' => 'Legacy Published Title', 'ar' => 'العنوان المنشور'],
            'content' => ['en' => 'Legacy Published Content', 'ar' => 'المحتوى المنشور'],
            'slug' => 'legacy-published',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => true,
        ]);

        $response = $this->getJson('/api/v1/blog');

        $response->assertStatus(200)
                 ->assertJsonFragment(['slug' => 'legacy-published']);
    }

    /** @test */
    public function existing_unpublished_posts_remain_hidden()
    {
        $post = Post::create([
            'title' => ['en' => 'Legacy Unpublished Title', 'ar' => 'العنوان غير المنشور'],
            'content' => ['en' => 'Legacy Unpublished Content', 'ar' => 'المحتوى غير المنشور'],
            'slug' => 'legacy-unpublished',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => false,
        ]);

        $response = $this->getJson('/api/v1/blog');

        $response->assertStatus(200)
                 ->assertJsonMissing(['slug' => 'legacy-unpublished']);
    }

    /** @test */
    public function existing_blog_detail_response_does_not_expose_ai_fields()
    {
        $post = Post::create([
            'title' => ['en' => 'Legacy Details', 'ar' => 'العنوان بالتفاصيل'],
            'content' => ['en' => 'Legacy Content', 'ar' => 'محتوى التفاصيل'],
            'slug' => 'legacy-details',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => true,
            'ai_generated' => true,
            'ai_status' => 'approved',
            'ai_metadata' => ['keyword' => 'cloud server'],
            'image_metadata' => ['alt' => 'cloud computing illustration'],
        ]);

        $response = $this->getJson("/api/v1/blog/{$post->slug}");

        $response->assertStatus(200)
                 ->assertJsonPath('data.slug', 'legacy-details')
                 ->assertJsonMissingPath('data.ai_generated')
                 ->assertJsonMissingPath('data.ai_status')
                 ->assertJsonMissingPath('data.ai_metadata')
                 ->assertJsonMissingPath('data.image_metadata');
    }

    /** @test */
    public function manual_post_creation_still_works()
    {
        $post = Post::create([
            'title' => ['en' => 'Manual Post Title', 'ar' => 'عنوان المقال اليدوي'],
            'content' => ['en' => 'Manual Post Content', 'ar' => 'محتوى المقال اليدوي'],
            'slug' => 'manual-post',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => false,
        ]);

        $this->assertDatabaseHas('posts', ['slug' => 'manual-post']);
        $this->assertFalse((bool) $post->ai_generated);
    }

    /** @test */
    public function manual_post_publish_false_to_true_still_works()
    {
        $post = Post::create([
            'title' => ['en' => 'Draft Title', 'ar' => 'العنوان المسودة'],
            'content' => ['en' => 'Draft Content', 'ar' => 'محتوى المسودة'],
            'slug' => 'draft-to-published',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => false,
        ]);

        $post->update(['is_published' => true]);

        $this->assertTrue((bool) $post->fresh()->is_published);
    }

    /** @test */
    public function manual_post_unpublish_true_to_false_still_works()
    {
        $post = Post::create([
            'title' => ['en' => 'Published Title', 'ar' => 'العنوان المنشور'],
            'content' => ['en' => 'Published Content', 'ar' => 'محتوى المنشور'],
            'slug' => 'published-to-draft',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => true,
        ]);

        $post->update(['is_published' => false]);

        $this->assertFalse((bool) $post->fresh()->is_published);
    }

    /** @test */
    public function publication_changes_do_not_modify_ai_metadata()
    {
        $post = Post::create([
            'title' => ['en' => 'Published AI Title', 'ar' => 'العنوان المنشور'],
            'content' => ['en' => 'Published AI Content', 'ar' => 'محتوى المنشور'],
            'slug' => 'published-ai-metadata',
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => true,
            'ai_generated' => true,
            'ai_status' => 'approved',
            'ai_metadata' => ['score' => 95],
        ]);

        $post->update(['is_published' => false]);

        $post = $post->fresh();
        $this->assertFalse((bool) $post->is_published);
        $this->assertEquals(['score' => 95], $post->ai_metadata);
    }

    /** @test */
    public function generated_post_service_overrides_incoming_is_published_true()
    {
        $post = GeneratedPostService::createDraft([
            'title' => ['en' => 'Forced Draft Title', 'ar' => 'العنوان المجبور مسودة'],
            'content' => ['en' => 'Forced Draft Content', 'ar' => 'محتوى المقال المجبور'],
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
            'is_published' => true, // Sent as true, must be overridden to false
        ]);

        $this->assertFalse((bool) $post->is_published);
        $this->assertTrue((bool) $post->ai_generated);
        $this->assertEquals('pending_review', $post->ai_status);
    }

    /** @test */
    public function generated_posts_have_ai_generated_true_and_pending_review_status()
    {
        $post = GeneratedPostService::createDraft([
            'title' => ['en' => 'Service Draft Title', 'ar' => 'العنوان'],
            'content' => ['en' => 'Service Content', 'ar' => 'المحتوى'],
            'blog_category_id' => $this->category->id,
            'author_id' => $this->author->id,
        ]);

        $this->assertTrue((bool) $post->ai_generated);
        $this->assertEquals('pending_review', $post->ai_status);
    }

    /** @test */
    public function ai_credential_api_token_is_encrypted_in_the_database()
    {
        $credential = new AiCredential();
        $credential->provider = 'gemini';
        $credential->setApiToken('my-secret-api-token-12345');
        $credential->save();

        // Query raw string from DB to prove it is encrypted
        $rawRow = \Illuminate\Support\Facades\DB::table('ai_credentials')
            ->where('id', $credential->id)
            ->first();

        $this->assertNotEquals('my-secret-api-token-12345', $rawRow->api_token);
        $this->assertEquals('my-secret-api-token-12345', Crypt::decryptString($rawRow->api_token));
        $this->assertEquals('2345', $rawRow->token_hint); // Last 4 chars
    }

    /** @test */
    public function api_token_is_absent_from_model_arrays_and_json()
    {
        $credential = new AiCredential();
        $credential->provider = 'openai';
        $credential->setApiToken('another-secret-token');
        $credential->save();

        $array = $credential->toArray();
        $json = json_encode($credential);

        $this->assertArrayNotHasKey('api_token', $array);
        $this->assertStringNotContainsString('another-secret-token', $json);
        $this->assertStringNotContainsString('api_token', $json);
    }

    /** @test */
    public function request_normalization_produces_same_hash_for_equivalent_keyword_input()
    {
        $payload1 = [
            'provider' => 'OpenAi ',
            'model' => 'GPT-4',
            'generation_mode' => 'one_per_keyword',
            'idempotency_key' => 'uuid-1',
            'language' => 'en',
            'keywords' => [
                ['main_keyword' => 'cloud development', 'secondary_keywords' => ['aws', 'scaling']],
                ['main_keyword' => 'artificial intelligence', 'secondary_keywords' => ['nlp', 'ml']]
            ],
            'settings_snapshot' => [
                'temperature' => 0.8,
                'length' => 1500
            ]
        ];

        // Equivalent input with different capitalization, spacing, and key order
        $payload2 = [
            'idempotency_key' => 'uuid-2', // Different idempotency key, should be ignored in hash
            'provider' => 'openai',
            'model' => 'gpt-4',
            'generation_mode' => 'one_per_keyword',
            'language' => 'EN',
            'keywords' => [
                ['secondary_keywords' => ['ML', 'nlp'], 'main_keyword' => ' artificial intelligence'],
                ['secondary_keywords' => ['scaling', 'AWS'], 'main_keyword' => 'cloud development ']
            ],
            'settings_snapshot' => [
                'length' => 1500,
                'temperature' => 0.8
            ]
        ];

        $hash1 = GenerationRequestNormalizer::calculateHash($payload1);
        $hash2 = GenerationRequestNormalizer::calculateHash($payload2);

        $this->assertEquals($hash1, $hash2);
    }

    /** @test */
    public function request_normalization_produces_different_hash_when_meaningful_settings_change()
    {
        $payload1 = [
            'provider' => 'gemini',
            'generation_mode' => 'one_per_keyword',
            'keywords' => [['main_keyword' => 'seo writing', 'secondary_keywords' => ['keywords']]],
            'settings_snapshot' => ['temperature' => 0.7]
        ];

        $payload2 = [
            'provider' => 'gemini',
            'generation_mode' => 'one_per_keyword',
            'keywords' => [['main_keyword' => 'seo writing', 'secondary_keywords' => ['keywords']]],
            'settings_snapshot' => ['temperature' => 0.9] // Different temperature
        ];

        $hash1 = GenerationRequestNormalizer::calculateHash($payload1);
        $hash2 = GenerationRequestNormalizer::calculateHash($payload2);

        $this->assertNotEquals($hash1, $hash2);
    }
}
