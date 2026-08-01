<?php

namespace Tests\Feature;

use App\Filament\Pages\AiPageBuilder;
use App\Jobs\GenerateAiPageJob;
use App\Models\AiCredential;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Services\Ai\BrandContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Unit\AiPageContentServiceTest;

class AiPageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/ai']);
        $this->admin = User::factory()->create();
    }

    #[Test]
    public function filament_page_defaults_to_a_safe_review_first_workflow_and_queues_a_page(): void
    {
        Queue::fake();
        $this->credential();
        $this->actingAs($this->admin);

        Livewire::test(AiPageBuilder::class)
            ->assertSet('data.page_type', 'landing_page')
            ->assertSet('data.page_template', 'custom')
            ->assertSet('data.page_depth', 'standard')
            ->assertSet('data.arabic_dialect', 'egyptian')
            ->assertSet('data.auto_publish', false)
            ->set('data.page_brief', 'Create a conversion landing page for ecommerce design with a practical buying journey and consultation CTA.')
            ->set('data.cta_url', '/contact-us')
            ->set('data.provider', 'openai')
            ->set('data.model', 'gpt-4o-mini')
            ->call('generate')
            ->assertHasNoFormErrors();

        $page = Page::latest('id')->firstOrFail();
        $this->assertTrue($page->ai_generated);
        $this->assertFalse($page->is_published);
        $this->assertSame('queued', $page->ai_status);
        $this->assertSame('custom', $page->ai_metadata['brief']['page_template']);
        $this->assertStringStartsWith('Create a conversion landing page', $page->ai_metadata['brief']['primary_keyword']);
        $this->assertSame('', $page->ai_metadata['brief']['target_audience']);
        $this->assertFalse((bool) $page->ai_metadata['brief']['auto_publish']);
        Queue::assertPushed(GenerateAiPageJob::class, fn (GenerateAiPageJob $job): bool => $job->pageId === $page->id);
    }

    #[Test]
    public function website_wide_context_retrieves_relevant_published_articles(): void
    {
        Post::create([
            'title' => ['en' => 'Ecommerce launch checklist', 'ar' => 'قائمة إطلاق المتجر الإلكتروني'],
            'slug' => 'ecommerce-launch-checklist',
            'content' => ['en' => 'A practical ecommerce planning workflow.', 'ar' => 'خطوات عملية لتخطيط المتجر الإلكتروني.'],
            'is_published' => true,
        ]);
        Post::create([
            'title' => ['en' => 'Hidden draft', 'ar' => 'مسودة مخفية'],
            'slug' => 'hidden-draft',
            'content' => ['en' => 'Ecommerce draft content.', 'ar' => 'محتوى متجر غير منشور.'],
            'is_published' => false,
        ]);

        $context = BrandContextService::build('ecommerce planning', 'both', websiteWide: true);

        $this->assertStringContainsString('ecommerce-launch-checklist', $context);
        $this->assertStringNotContainsString('hidden-draft', $context);
        $this->assertStringContainsString('relevant_articles', $context);
    }

    #[Test]
    public function job_generates_a_sanitized_bilingual_review_draft_that_is_hidden_from_the_public_api(): void
    {
        $this->credential();
        $page = $this->queuedPage(false);
        $response = AiPageContentServiceTest::validResponse();
        $response['blocks'][1]['data']['content']['ar'] .= '<script>alert(1)</script>';
        Http::fake(['api.openai.com/*' => Http::response($this->openAiResponse($response), 200)]);

        (new GenerateAiPageJob($page->id))->handle();

        $page->refresh();
        $this->assertSame('pending_review', $page->ai_status);
        $this->assertFalse($page->is_published);
        $this->assertSame('custom', $page->sections['template']);
        $this->assertCount(7, $page->sections['blocks']);
        $this->assertSame($page->sections['blocks'], $page->sections['en']);
        $this->assertStringNotContainsString('<script', $page->sections['blocks'][1]['data']['content']['ar']);
        $this->assertSame(246, $page->ai_metadata['usage_metadata']['total_tokens']);
        $this->getJson('/api/v1/pages/' . $page->slug . '?lang=en')->assertNotFound();

        $this->actingAs($this->admin);
        Livewire::test(\App\Filament\Resources\PageBuilderResource\Pages\EditPage::class, ['record' => $page->getRouteKey()])
            ->set('data.is_published', true)
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertTrue($page->fresh()->is_published);
        $this->assertSame('approved', $page->fresh()->ai_status);
    }

    #[Test]
    public function auto_publish_exposes_the_completed_page_and_custom_blocks_are_returned(): void
    {
        $this->credential();
        $page = $this->queuedPage(true);
        Http::fake(['api.openai.com/*' => Http::response($this->openAiResponse(AiPageContentServiceTest::validResponse()), 200)]);

        (new GenerateAiPageJob($page->id))->handle();

        $page->refresh();
        $this->assertTrue($page->is_published);
        $this->assertSame('approved', $page->ai_status);
        $this->getJson('/api/v1/pages/' . $page->slug . '?lang=ar')
            ->assertOk()
            ->assertJsonPath('data.title', 'صفحة نمو المتاجر الإلكترونية')
            ->assertJsonCount(7, 'data.sections.blocks')
            ->assertJsonPath('data.sections.blocks.0.type', 'hero')
            ->assertJsonPath('data.sections.blocks.0.data.title.ar', 'ابني متجر إلكتروني يحول الزيارات لطلبات');
    }

    #[Test]
    public function invalid_ai_output_marks_the_page_failed_and_never_publishes_it(): void
    {
        $this->credential();
        $page = $this->queuedPage(true);
        Http::fake(['api.openai.com/*' => Http::response($this->openAiResponse([
            'title' => ['en' => 'Incomplete', 'ar' => 'غير مكتملة'],
        ]), 200)]);

        try {
            (new GenerateAiPageJob($page->id))->handle();
            $this->fail('The invalid AI response should have failed validation.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('subtitle', $exception->getMessage());
        }

        $page->refresh();
        $this->assertSame('failed', $page->ai_status);
        $this->assertFalse($page->is_published);
        $this->assertNotEmpty($page->ai_metadata['error']);
    }

    private function credential(): AiCredential
    {
        $credential = new AiCredential([
            'provider' => 'openai',
            'default_model' => 'gpt-4o-mini',
            'is_active' => true,
        ]);
        $credential->setApiToken('valid-openai-key');
        $credential->save();
        return $credential;
    }

    private function queuedPage(bool $autoPublish): Page
    {
        return Page::create([
            'slug' => 'queued-ai-page-' . ($autoPublish ? 'published' : 'draft'),
            'title' => ['en' => 'Generating', 'ar' => 'جاري الإنشاء'],
            'subtitle' => ['en' => 'Generating', 'ar' => 'جاري الإنشاء'],
            'sections' => ['template' => 'custom', 'blocks' => []],
            'is_published' => false,
            'ai_generated' => true,
            'ai_status' => 'queued',
            'ai_metadata' => [
                'provider' => 'openai',
                'model' => 'gpt-4o-mini',
                'brief' => [
                    'page_brief' => 'Build a complete ecommerce design landing page grounded in Serv5 services.',
                    'page_type' => 'landing_page',
                    'primary_keyword' => 'Ecommerce Website Design',
                    'target_audience' => 'Business owners planning an online store.',
                    'page_depth' => 'standard',
                    'tone' => 'confident',
                    'arabic_dialect' => 'egyptian',
                    'cta_url' => '/contact-us',
                    'verified_facts' => '',
                    'auto_publish' => $autoPublish,
                    'temperature' => 0.6,
                ],
            ],
        ]);
    }

    private function openAiResponse(array $response): array
    {
        return [
            'choices' => [['message' => ['content' => json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)]]],
            'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 146, 'total_tokens' => 246],
        ];
    }
}
