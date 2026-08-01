<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\AiCredential;
use App\Models\AiGenerationRequest;
use App\Models\AiGenerationItem;
use App\Jobs\GenerateArticleJob;
use App\Jobs\PlanTopicClusterJob;
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\GeneratedPostService;
use App\Services\Ai\GenerationRequestNormalizer;
use App\Services\Ai\InternalLinkService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Crypt;
use Livewire\Livewire;

class AiWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected BlogCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        // Load AI migrations during test setup
        $this->artisan('migrate', ['--path' => 'database/migrations/ai']);

        $this->admin = User::factory()->create();
        $this->category = BlogCategory::create([
            'name' => ['en' => 'Business', 'ar' => 'أعمال'],
            'slug' => 'business',
        ]);
    }

    /** @test */
    public function provider_manager_resolves_gemini_openai_deepseek_correctly()
    {
        $this->assertInstanceOf(\App\Services\Ai\GeminiProvider::class, AiProviderManager::getProvider('gemini'));
        $this->assertInstanceOf(\App\Services\Ai\OpenAiProvider::class, AiProviderManager::getProvider('openai'));
        $this->assertInstanceOf(\App\Services\Ai\DeepSeekProvider::class, AiProviderManager::getProvider('deepseek'));
    }

    /** @test */
    public function provider_test_connection_returns_success_correctly()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['candidates' => []], 200),
            'api.openai.com/*' => Http::response([], 200),
        ]);

        $gemini = AiProviderManager::getProvider('gemini');
        $openai = AiProviderManager::getProvider('openai');

        $this->assertTrue($gemini->testConnection('fake-gemini-key'));
        $this->assertTrue($openai->testConnection('fake-openai-key'));
    }

    /** @test */
    public function provider_test_connection_returns_failure_correctly()
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([], 500),
            'api.openai.com/*' => Http::response([], 401),
        ]);

        $gemini = AiProviderManager::getProvider('gemini');
        $openai = AiProviderManager::getProvider('openai');

        $this->assertFalse($gemini->testConnection('bad-gemini-key'));
        $this->assertFalse($openai->testConnection('bad-openai-key'));
    }

    /** @test */
    public function article_content_service_validates_arabic_seo_correctly()
    {
        // Valid article structure for AR language mode
        $validAr = [
            'title_ar' => 'العنوان الصحيح لخدمات التصميم',
            'slug' => 'design-services-slug',
            'seo_title_ar' => 'عنوان سيو عربي مثالي وصحيح للبحث ومحرك السيو',
            'meta_description_ar' => 'هذا وصف سيو عربي مثالي يحتوي على كلمات مفتاحية لغرض محركات البحث والتحقق من الخدمة.',
            'introduction_ar' => 'المقدمة هنا تحتوي على الكلمة المفتاحية تصميم الخدمات للتعريف بأهمية تصميم المواقع والشركات وكيف تفيد في الترويج.',
            'body_ar' => '<h2>العنوان الفرعي الأول</h2><p>هذا النص يمثل الفقرة الأولى. تصميم الخدمات هو فكرة هامة للغاية. ويوفر حلولاً للمشاكل المعتادة بطرق متقدمة وتفصيلية تناسب الجميع.</p><h2>العنوان الثاني</h2><p>هذا النص يمثل الفقرة الثانية. ونحتاج إلى تصميم الخدمات لنستطيع توفير الخدمات الممتازة لكل العملاء المهتمين بالسوق.</p>',
            'faq' => [
                ['question_ar' => 'س1', 'answer_ar' => 'ج1'],
                ['question_ar' => 'س2', 'answer_ar' => 'ج2'],
                ['question_ar' => 'س3', 'answer_ar' => 'ج3'],
                ['question_ar' => 'س4', 'answer_ar' => 'ج4'],
                ['question_ar' => 'س5', 'answer_ar' => 'ج5'],
            ]
        ];

        $report = ArticleContentService::validateAndGenerateReport($validAr, 'تصميم الخدمات', 'ar');
        $this->assertEmpty($report['errors']);

        // Invalid: contains forbidden H1 tags, raising Hard Error
        $invalidH1 = $validAr;
        $invalidH1['body_ar'] = '<h1>العنوان الأول الممنوع</h1><p>المحتوى الممنوع</p>';
        $reportH1 = ArticleContentService::validateAndGenerateReport($invalidH1, 'تصميم الخدمات', 'ar');
        $this->assertContains("Content contains an <h1> tag, which is forbidden to avoid duplicate titles (Page 2 & 8 of PDF).", $reportH1['errors']);

        // Invalid: contains unauthorized external link, raising Hard Error
        $invalidLink = $validAr;
        $invalidLink['body_ar'] = '<p>تابعونا على <a href="http://malicious-spam-domain.com/buy">هذا الرابط الخارجي الممنوع</a></p>';
        $reportLink = ArticleContentService::validateAndGenerateReport($invalidLink, 'تصميم الخدمات', 'ar');
        $this->assertContains("Unauthorized external link detected: 'http://malicious-spam-domain.com/buy' (Page 6 of PDF).", $reportLink['errors']);

        // Warning test: contains AI cliché "لا شك أن" and Emojis
        $warningContent = $validAr;
        $warningContent['body_ar'] = '<h2>العنوان الفرعي</h2><p>لا شك أن هذا المقال رائع جداً ومفيد! 😊</p>';
        $reportWarning = ArticleContentService::validateAndGenerateReport($warningContent, 'تصميم الخدمات', 'ar');
        $this->assertCount(0, $reportWarning['errors']);
        $this->assertTrue(count($reportWarning['warnings']) > 0);
    }

    /** @test */
    public function generate_article_job_executes_successfully_and_saves_unpublished_draft()
    {
        // Setup credential
        $credential = new AiCredential();
        $credential->provider = 'openai';
        $credential->setApiToken('valid-openai-key-abcde');
        $credential->is_active = true;
        $credential->save();

        // Setup request and item in AR mode
        $request = AiGenerationRequest::create([
            'created_by' => $this->admin->id,
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'generation_mode' => 'one_per_keyword',
            'idempotency_key' => 'unique-req-uuid-1',
            'request_hash' => 'dummy-hash-1',
            'main_keyword' => 'قواعد البيانات',
            'language' => 'ar',
            'article_count' => 1,
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
            'status' => 'pending',
            'total_items' => 1,
        ]);

        $item = AiGenerationItem::create([
            'request_id' => $request->id,
            'keyword' => 'قواعد البيانات',
            'status' => 'pending',
        ]);

        // Fake OpenAI response returning AR fields only, EN fields null
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'title_ar' => 'دليل قواعد البيانات السحابية',
                                'title_en' => null,
                                'slug' => 'cloud-databases-guide',
                                'seo_title_ar' => 'عنوان السيو لقواعد البيانات السحابية الممتازة',
                                'seo_title_en' => null,
                                'meta_description_ar' => 'وصف سيو عربي لقواعد البيانات السحابية يحتوي على الكلمات المناسبة والتفاصيل.',
                                'meta_description_en' => null,
                                'introduction_ar' => 'هذه مقدمة ممتازة عن قواعد البيانات السحابية التي تفيد الشركات الناشئة بشكل كبير.',
                                'introduction_en' => null,
                                'body_ar' => '<h2>أهمية قواعد البيانات</h2><p>هذا النص يمثل الفقرة الأولى. قواعد البيانات السحابية توفر حلا للمشاكل بطرق متطورة وتفصيلية تناسب الجميع.</p><h2>مستقبل قواعد البيانات</h2><p>هذا النص يمثل الفقرة الثانية. ونحتاج إلى قواعد البيانات لنستطيع توفير الخدمات الممتازة لكل المهتمين بالسوق.</p>',
                                'body_en' => null,
                                'faq' => [
                                    ['question_ar' => 'س1', 'answer_ar' => 'ج1'],
                                    ['question_ar' => 'س2', 'answer_ar' => 'ج2'],
                                    ['question_ar' => 'س3', 'answer_ar' => 'ج3'],
                                    ['question_ar' => 'س4', 'answer_ar' => 'ج4'],
                                    ['question_ar' => 'س5', 'answer_ar' => 'ج5'],
                                ]
                            ])
                        ]
                    ]
                ],
                'usage' => [
                    'prompt_tokens' => 100,
                    'completion_tokens' => 200,
                    'total_tokens' => 300
                ]
            ], 200)
        ]);

        // Execute Job
        (new GenerateArticleJob($item->id))->handle();

        // Assert item was completed successfully
        $item->refresh();
        $this->assertEquals('completed', $item->status);
        $this->assertNotNull($item->post_id);

        // Assert parent request progress updated
        $request->refresh();
        $this->assertEquals(1, $request->completed_items);
        $this->assertEquals('completed', $request->status);

        // Assert post properties are forced drafts
        $post = $item->post;
        $this->assertFalse((bool) $post->is_published);
        $this->assertTrue((bool) $post->ai_generated);
        $this->assertEquals('pending_review', $post->ai_status);
        $this->assertEquals('دليل قواعد البيانات السحابية', $post->title['ar']);
        $this->assertNull($post->title['en']);

        // Assert introduction and body combined exactly once
        $this->assertStringStartsWith('<p>هذه مقدمة ممتازة عن قواعد البيانات السحابية التي تفيد الشركات الناشئة بشكل كبير.</p>', $post->content['ar']);
        $this->assertStringContainsString('<h2>أهمية قواعد البيانات</h2>', $post->content['ar']);
        $this->assertStringContainsString('class="article-faq"', $post->content['ar']);
        $this->assertSame(5, substr_count($post->content['ar'], 'class="article-faq-item"'));
        $this->assertCount(5, $post->ai_metadata['faq']);
        $this->assertGreaterThanOrEqual(3, substr_count($post->content['ar'], '<a href='));
        $this->assertNull($post->content['en']);
    }

    /** @test */
    public function generate_article_job_fails_gracefully_on_api_errors_and_allows_retry()
    {
        // Setup credential
        $credential = new AiCredential();
        $credential->provider = 'gemini';
        $credential->setApiToken('valid-gemini-key');
        $credential->is_active = true;
        $credential->save();

        $request = AiGenerationRequest::create([
            'created_by' => $this->admin->id,
            'provider' => 'gemini',
            'model' => 'gemini-1.5-flash',
            'generation_mode' => 'one_per_keyword',
            'idempotency_key' => 'unique-req-uuid-2',
            'request_hash' => 'dummy-hash-2',
            'main_keyword' => 'cloud servers',
            'language' => 'ar',
            'article_count' => 1,
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
            'status' => 'pending',
            'total_items' => 1,
        ]);

        $item = AiGenerationItem::create([
            'request_id' => $request->id,
            'keyword' => 'cloud servers',
            'status' => 'pending',
        ]);

        // Fake API exception
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response('Quota Exceeded', 429)
        ]);

        try {
            (new GenerateArticleJob($item->id))->handle();
            $this->fail("Expected job exception to be thrown.");
        } catch (\Throwable $e) {
            // Expected
        }

        $item->refresh();
        // A direct first attempt remains pending so Laravel can apply the
        // configured queue retry/backoff policy.
        $this->assertEquals('pending', $item->status);
        $this->assertStringContainsString('Quota Exceeded', $item->error_message);

        $request->refresh();
        $this->assertEquals('generating', $request->status);
        $this->assertEquals(0, $request->failed_items);
    }

    /** @test */
    public function filament_ui_dashboard_presets_and_default_properties()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Filament\Pages\AiGenerationDashboard::class)
            ->assertSet('data.generation_type', 'one_article')
            ->assertSet('data.arabic_dialect', 'egyptian')
            ->assertSet('data.article_length_preset', 'standard')
            ->assertSet('data.creativity_preset', 'balanced')
            ->assertSet('data.suggest_keywords', true)
            ->assertSet('data.auto_publish', false);
    }

    /** @test */
    public function internal_backlink_enforcement_is_idempotent(): void
    {
        foreach (range(1, 4) as $index) {
            Post::create([
                'title' => ['en' => "Related Article {$index}", 'ar' => "مقال مرتبط {$index}"],
                'slug' => "related-article-{$index}",
                'content' => ['en' => 'Useful content', 'ar' => 'محتوى مفيد'],
                'blog_category_id' => $this->category->id,
                'author_id' => $this->admin->id,
                'is_published' => true,
            ]);
        }

        $article = [
            'introduction_ar' => 'مقدمة عملية عن بناء المواقع للشركات.',
            'body_ar' => '<h2>خطة التنفيذ</h2><p>اختر الحل المناسب حسب احتياجات المشروع والميزانية.</p>',
        ];

        $first = InternalLinkService::apply($article, 'ar', 'بناء المواقع');
        $second = InternalLinkService::apply($first, 'ar', 'بناء المواقع');

        preg_match_all('/href="([^"]+)"/', $second['body_ar'], $matches);
        $this->assertGreaterThanOrEqual(3, count(array_unique($matches[1])));
        $this->assertSame(count($matches[1]), count(array_unique($matches[1])));
        $types = array_count_values(array_column($second['internal_link_suggestions'], 'type'));
        $this->assertGreaterThanOrEqual(3, $types['post'] ?? 0);
        $this->assertGreaterThanOrEqual(3, $types['service'] ?? 0);
        $this->assertGreaterThanOrEqual(2, $types['page'] ?? 0);
        $this->assertContains('https://serv5.com.eg/ar/', array_column($second['internal_link_suggestions'], 'url'));
    }

    /** @test */
    public function topic_cluster_planner_creates_a_pillar_and_the_exact_supporting_articles(): void
    {
        Queue::fake();
        $credential = new AiCredential();
        $credential->provider = 'openai';
        $credential->setApiToken('valid-openai-key-abcde');
        $credential->is_active = true;
        $credential->save();

        $request = AiGenerationRequest::create([
            'created_by' => $this->admin->id,
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'generation_mode' => 'topic_cluster',
            'idempotency_key' => 'cluster-plan-uuid',
            'request_hash' => 'cluster-plan-hash',
            'settings_snapshot' => ['arabic_dialect' => 'egyptian', 'suggest_keywords' => true, 'temperature' => 0.7],
            'main_keyword' => 'تصميم متجر إلكتروني احترافي',
            'secondary_keywords' => ['بوابات الدفع'],
            'language' => 'ar',
            'article_count' => 4,
            'category_id' => $this->category->id,
            'author_id' => $this->admin->id,
            'status' => 'pending',
            'total_items' => 4,
        ]);

        Http::fake(['api.openai.com/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode([
                'subtopics' => [
                    ['keyword' => 'اختيار بوابة الدفع المناسبة', 'search_intent' => 'commercial', 'angle' => 'مقارنة خيارات الدفع'],
                    ['keyword' => 'تقليل خطوات إتمام الشراء', 'search_intent' => 'transactional', 'angle' => 'رفع التحويل'],
                    ['keyword' => 'تهيئة المتجر لمحركات البحث', 'search_intent' => 'informational', 'angle' => 'بناء الظهور العضوي'],
                ],
            ], JSON_UNESCAPED_UNICODE)]]],
            'usage' => ['prompt_tokens' => 50, 'completion_tokens' => 80, 'total_tokens' => 130],
        ], 200)]);

        (new PlanTopicClusterJob($request->id))->handle();

        $request->refresh();
        $this->assertSame('generating', $request->status);
        $this->assertSame(4, $request->items()->count());
        $this->assertSame('تصميم متجر إلكتروني احترافي', $request->items()->orderBy('id')->first()->keyword);
        $this->assertCount(4, $request->settings_snapshot['cluster_plan']);
        Queue::assertPushed(GenerateArticleJob::class, 4);
    }

    /** @test */
    public function dashboard_counts_the_pillar_plus_requested_supporting_articles(): void
    {
        Queue::fake();
        $credential = new AiCredential();
        $credential->provider = 'openai';
        $credential->default_model = 'gpt-4o-mini';
        $credential->max_articles_per_request = 10;
        $credential->setApiToken('valid-openai-key-abcde');
        $credential->is_active = true;
        $credential->save();

        $this->actingAs($this->admin);
        Livewire::test(\App\Filament\Pages\AiGenerationDashboard::class)
            ->set('data.generation_type', 'topic_cluster')
            ->set('data.main_keyword', 'تصميم متجر إلكتروني احترافي')
            ->set('data.article_count', 3)
            ->set('data.category_id', $this->category->id)
            ->set('data.author_id', $this->admin->id)
            ->set('data.language', 'ar')
            ->set('data.arabic_dialect', 'egyptian')
            ->set('data.auto_publish', true)
            ->set('data.provider', 'openai')
            ->set('data.model', 'gpt-4o-mini')
            ->call('generate')
            ->assertHasNoFormErrors();

        $request = AiGenerationRequest::latest('id')->firstOrFail();
        $this->assertSame(4, $request->article_count);
        $this->assertSame(4, $request->total_items);
        $this->assertTrue((bool) ($request->settings_snapshot['auto_publish'] ?? false));
        $this->assertSame(0, $request->items()->count());
        Queue::assertPushed(PlanTopicClusterJob::class, 1);
    }

    /** @test */
    public function filament_ui_credentials_form_reactiveness()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Filament\Resources\AiCredentialResource\Pages\CreateAiCredential::class)
            ->set('data.provider', 'gemini')
            ->assertSet('data.default_model', null)
            ->set('data.provider', 'openai')
            ->assertSet('data.default_model', null);
    }
}
