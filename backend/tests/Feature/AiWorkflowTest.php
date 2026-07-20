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
use App\Services\Ai\AiProviderManager;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\GeneratedPostService;
use App\Services\Ai\GenerationRequestNormalizer;
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
        $this->assertEquals('failed', $item->status);
        $this->assertStringContainsString('Quota Exceeded', $item->error_message);

        $request->refresh();
        $this->assertEquals('failed', $request->status);
        $this->assertEquals(1, $request->failed_items);
    }

    /** @test */
    public function filament_ui_dashboard_presets_and_default_properties()
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Filament\Pages\AiGenerationDashboard::class)
            ->assertSet('data.generation_type', 'one_article')
            ->assertSet('data.article_length_preset', 'standard')
            ->assertSet('data.creativity_preset', 'balanced')
            ->assertSet('data.suggest_keywords', true);
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
