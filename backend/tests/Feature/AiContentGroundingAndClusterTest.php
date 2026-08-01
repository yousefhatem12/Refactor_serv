<?php

namespace Tests\Feature;

use App\Jobs\FinalizeTopicClusterLinksJob;
use App\Models\AiGenerationItem;
use App\Models\AiGenerationRequest;
use App\Models\BlogCategory;
use App\Models\Post;
use App\Models\Service;
use App\Models\User;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\BrandContextService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiContentGroundingAndClusterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/ai']);
    }

    public function test_brand_context_uses_real_active_service_data(): void
    {
        Service::create([
            'name' => ['ar' => 'استضافة المواقع', 'en' => 'Web Hosting'],
            'slug' => 'web-hosting',
            'description' => ['ar' => 'استضافة فعلية مُدارة للمواقع', 'en' => 'Managed hosting for websites'],
            'is_active' => true,
        ]);
        Service::create([
            'name' => ['ar' => 'خدمة متوقفة', 'en' => 'Inactive Service'],
            'slug' => 'inactive-service',
            'description' => ['ar' => 'يجب ألا تظهر', 'en' => 'Must not appear'],
            'is_active' => false,
        ]);

        $context = BrandContextService::build('استضافة المواقع', 'ar');

        $this->assertStringContainsString('استضافة فعلية مُدارة للمواقع', $context);
        $this->assertStringContainsString('https://serv5.com.eg/ar/services/services-web-hosting', $context);
        $this->assertStringNotContainsString('يجب ألا تظهر', $context);

        $prompt = ArticleContentService::buildPrompt('استضافة المواقع', [], 'ar', 800, 'egyptian', true, $context);
        $this->assertStringContainsString('SERV5 VERIFIED CONTEXT', $prompt);
        $this->assertStringContainsString('Never invent prices', $prompt);
    }

    public function test_faq_is_rendered_before_the_conclusion_and_schema_is_derived(): void
    {
        $article = $this->article('دليل الاستضافة', 'hosting-guide');
        $article['body_ar'] = '<h2>التفاصيل</h2><p>محتوى مفيد</p><h2>الخاتمة</h2><p>الخلاصة النهائية</p>';

        $rendered = ArticleContentService::renderFaqIntoBodies($article, 'ar');

        $faqPosition = strpos($rendered['body_ar'], 'class="article-faq"');
        $conclusionPosition = strpos($rendered['body_ar'], '<h2>الخاتمة</h2>');
        $this->assertNotFalse($faqPosition);
        $this->assertNotFalse($conclusionPosition);
        $this->assertLessThan($conclusionPosition, $faqPosition);
        $this->assertSame('FAQPage', $rendered['faq_schema']['ar']['@type']);
        $this->assertCount(5, $rendered['faq_schema']['ar']['mainEntity']);
    }

    public function test_cluster_finalizer_links_every_supporting_article_to_the_real_pillar_slug(): void
    {
        $user = User::factory()->create();
        $category = BlogCategory::create(['name' => ['ar' => 'تقنية', 'en' => 'Technology'], 'slug' => 'technology']);
        $request = AiGenerationRequest::create([
            'created_by' => $user->id,
            'provider' => 'openai',
            'model' => 'gpt-4o-mini',
            'generation_mode' => 'topic_cluster',
            'idempotency_key' => 'cluster-finalize-key',
            'request_hash' => 'cluster-finalize-hash',
            'main_keyword' => 'دليل استضافة المواقع',
            'language' => 'ar',
            'article_count' => 3,
            'category_id' => $category->id,
            'author_id' => $user->id,
            'status' => 'finalizing',
            'total_items' => 3,
            'completed_items' => 3,
        ]);

        $definitions = [
            ['دليل استضافة المواقع', 'hosting-pillar'],
            ['اختيار خطة الاستضافة المناسبة', 'choose-hosting-plan'],
            ['حماية مواقع الشركات', 'secure-business-websites'],
        ];
        foreach ($definitions as [$keyword, $slug]) {
            $article = ArticleContentService::renderFaqIntoBodies($this->article($keyword, $slug), 'ar');
            $post = Post::create([
                'title' => ['ar' => $keyword, 'en' => null],
                'slug' => $slug,
                'content' => ['ar' => '<p>' . $article['introduction_ar'] . '</p>' . $article['body_ar'], 'en' => null],
                'blog_category_id' => $category->id,
                'author_id' => $user->id,
                'is_published' => false,
                'ai_generated' => true,
                'ai_status' => 'pending_review',
                'ai_metadata' => ['faq' => $article['faq']],
            ]);
            AiGenerationItem::create([
                'request_id' => $request->id,
                'keyword' => $keyword,
                'status' => 'completed',
                'post_id' => $post->id,
                'parsed_response' => $article,
                'quality_report' => ['warnings' => [], 'errors' => []],
            ]);
        }

        (new FinalizeTopicClusterLinksJob($request->id))->handle();

        $request->refresh();
        $this->assertSame('completed', $request->status);
        $support = $request->items()->where('keyword', 'اختيار خطة الاستضافة المناسبة')->with('post')->firstOrFail();
        $this->assertStringContainsString('class="topic-cluster-links"', $support->post->content['ar']);
        $this->assertStringContainsString('https://serv5.com.eg/ar/blog/hosting-pillar', $support->post->content['ar']);
        $this->assertLessThan(
            strpos($support->post->content['ar'], 'class="article-faq"'),
            strpos($support->post->content['ar'], 'class="topic-cluster-links"')
        );
    }

    private function article(string $keyword, string $slug): array
    {
        return [
            'title_ar' => $keyword,
            'slug' => $slug,
            'seo_title_ar' => $keyword . ' بطريقة عملية ومتكاملة للشركات',
            'meta_description_ar' => $keyword . ' بشرح عملي يساعد الشركات على الاختيار والتنفيذ وإدارة الخدمة بوضوح. ابدأ الآن وتعرف على التفاصيل المناسبة لمشروعك.',
            'introduction_ar' => $keyword . ' موضوع مهم للشركات التي تريد بناء حضور رقمي مستقر. يوضح هذا الدليل خطوات الاختيار والتنفيذ مع التركيز على القرارات العملية وتجنب الأخطاء الشائعة بدون ادعاءات غير موثقة أو تفاصيل غير موجودة.',
            'body_ar' => '<h2>' . $keyword . '</h2><p>يوضح هذا القسم خطوات عملية ومباشرة تساعد فريق العمل على مقارنة الاحتياجات وترتيب الأولويات قبل اتخاذ القرار، مع مراجعة المتطلبات التقنية والتشغيلية بصورة واضحة.</p><h2>الخاتمة</h2><p>تلخص الخاتمة أهم خطوات ' . $keyword . ' وتدعو القارئ إلى مراجعة احتياجات مشروعه والتواصل للحصول على اختيار مناسب.</p>',
            'faq' => array_map(fn (int $number): array => [
                'question_ar' => "ما السؤال رقم {$number}؟",
                'answer_ar' => "هذه إجابة عملية ومباشرة للسؤال رقم {$number}.",
            ], range(1, 5)),
            'internal_link_suggestions' => [],
        ];
    }
}
