<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function it_returns_rich_content_with_structured_tables_and_lists()
    {
        $author = User::factory()->create();

        Post::create([
            'title' => ['en' => 'Formatted Post', 'ar' => 'مقال منسق'],
            'content' => [
                'en' => '<h2>Overview</h2>\\n<p style="text-align: center"><strong>Bold</strong> <u>underlined</u> <s>removed</s> H<sub>2</sub>O <a href="/guide">same tab</a> <a href="https://example.com" target="_blank">new tab</a></p><ol><li>First</li><li>Second</li></ol><ul><li>Alpha</li></ul><table><thead><tr><th>Name</th><th>Price</th></tr></thead><tbody><tr><td>Hosting</td><td>100</td></tr></tbody></table>',
                'ar' => '<p>محتوى</p>',
            ],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'formatted-post',
        ]);

        $response = $this->getJson('/api/v1/blog/formatted-post?lang=en');

        $response->assertOk()
            ->assertJsonPath('data.table.head', ['Name', 'Price'])
            ->assertJsonPath('data.table.rows.0.Name', 'Hosting')
            ->assertJsonPath('data.table.rows.0.Price', '100')
            ->assertJsonPath('data.list.0.type', 'ordered')
            ->assertJsonPath('data.list.0.list', ['First', 'Second'])
            ->assertJsonPath('data.list.1.type', 'unordered')
            ->assertJsonPath('data.list.1.list', ['Alpha']);

        $content = $response->json('data.content');
        $this->assertStringContainsString('<h2>Overview</h2>', $content);
        $this->assertStringContainsString('<strong>Bold</strong>', $content);
        $this->assertStringContainsString('<u>underlined</u>', $content);
        $this->assertStringContainsString('<del>removed</del>', $content);
        $this->assertStringContainsString('<sub>2</sub>', $content);
        $this->assertStringContainsString('target="_blank"', $content);
        $this->assertStringNotContainsString('\\n', $content);
    }

    #[Test]
    public function it_returns_null_structured_content_when_the_post_has_no_table_or_list()
    {
        $author = User::factory()->create();

        Post::create([
            'title' => ['en' => 'Plain Post', 'ar' => 'مقال بسيط'],
            'content' => ['en' => '<p>Plain content</p>', 'ar' => '<p>محتوى بسيط</p>'],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'plain-post',
        ]);

        $this->getJson('/api/v1/blog/plain-post?lang=en')
            ->assertOk()
            ->assertJsonPath('data.content', '<p>Plain content</p>')
            ->assertJsonPath('data.table', null)
            ->assertJsonPath('data.list', []);
    }

    #[Test]
    public function it_renders_filament_rich_editor_documents_as_html()
    {
        $author = User::factory()->create();

        Post::create([
            'title' => ['en' => 'Document Post', 'ar' => 'مقال مستند'],
            'content' => [
                'en' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['textAlign' => 'center'],
                        'content' => [[
                            'type' => 'text',
                            'text' => 'Centered bold text',
                            'marks' => [['type' => 'bold']],
                        ]],
                    ]],
                ],
                'ar' => '<p>محتوى</p>',
            ],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'document-post',
        ]);

        $response = $this->getJson('/api/v1/blog/document-post?lang=en');

        $response->assertOk();
        $this->assertStringContainsString('<p style="text-align: center;"><strong>Centered bold text</strong></p>', $response->json('data.content'));
    }

    #[Test]
    public function it_renders_inline_images_with_the_saved_size()
    {
        Storage::fake('public');

        $author = User::factory()->create();
        $imagePath = 'blog/content/article-image.webp';
        Storage::disk('public')->put($imagePath, 'fake image');

        Post::create([
            'title' => ['en' => 'Image Post', 'ar' => 'Ù…Ù‚Ø§Ù„ Ø¨ØµÙˆØ±Ø©'],
            'content' => [
                'en' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [[
                                'type' => 'text',
                                'text' => 'Before the image',
                            ]],
                        ],
                        [
                            'type' => 'image',
                            'attrs' => [
                                'id' => $imagePath,
                                'src' => null,
                                'alt' => 'Article image',
                                'width' => '420px',
                                'height' => '280px',
                            ],
                        ],
                    ],
                ],
                'ar' => '<p>Ù…Ø­ØªÙˆÙ‰</p>',
            ],
            'is_published' => true,
            'author_id' => $author->id,
            'slug' => 'image-post',
        ]);

        $response = $this->getJson('/api/v1/blog/image-post?lang=en');

        $response->assertOk();
        $content = $response->json('data.content');
        $imageUrl = Storage::disk('public')->url($imagePath);

        $this->assertStringContainsString('<img', $content);
        $this->assertStringContainsString('src="' . $imageUrl . '"', $content);
        $this->assertStringContainsString('data-id="' . $imagePath . '"', $content);
        $this->assertStringContainsString('width="420px"', $content);
        $this->assertStringContainsString('height="280px"', $content);
    }
}
