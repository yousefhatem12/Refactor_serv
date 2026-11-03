<?php

namespace Tests\Unit;

use App\Services\Ai\ArticleContentService;
use App\Services\Ai\ArabicDialectCatalog;
use PHPUnit\Framework\TestCase;

class ArticleContentServiceOriginalityTest extends TestCase
{
    public function test_dialect_catalog_covers_major_arab_regions_and_keeps_egyptian_default(): void
    {
        $options = ArabicDialectCatalog::options();

        $this->assertGreaterThanOrEqual(25, count($options));
        foreach (['egyptian', 'saudi', 'hijazi', 'najdi', 'gulf', 'iraqi', 'levantine', 'sudanese', 'moroccan', 'yemeni'] as $dialect) {
            $this->assertArrayHasKey($dialect, $options);
        }
        $this->assertStringContainsString('Default', $options['egyptian']);
    }

    public function test_prompt_requires_original_editorial_value_in_both_languages(): void
    {
        $prompt = ArticleContentService::buildPrompt(
            'تحسين محركات البحث للمواقع',
            ['تصميم المواقع', 'التسويق الرقمي'],
            'both',
            1200
        );

        $this->assertStringContainsString('ORIGINALITY AND EDITORIAL VALUE', $prompt);
        $this->assertStringContainsString('Do not copy, quote, stitch together, translate, or lightly paraphrase', $prompt);
        $this->assertStringContainsString('English and Arabic must each be written natively', $prompt);
        $this->assertStringContainsString('في عالم اليوم', $prompt);
        $this->assertStringContainsString('professional Egyptian Arabic', $prompt);
        $this->assertStringContainsString('MANDATORY INTERNAL LINK MIX', $prompt);
        $this->assertStringContainsString('exactly 5', $prompt);
    }

    public function test_prompt_supports_the_saudi_dialect(): void
    {
        $prompt = ArticleContentService::buildPrompt('تصميم المواقع', [], 'ar', 1200, 'saudi');

        $this->assertStringContainsString('professional Saudi Arabic', $prompt);
        $this->assertStringNotContainsString('professional Egyptian Arabic', $prompt);
    }

    public function test_keyword_suggestion_toggle_changes_the_generation_contract(): void
    {
        $enabled = ArticleContentService::buildPrompt('web design', [], 'en', 1200, 'egyptian', true);
        $disabled = ArticleContentService::buildPrompt('web design', ['branding'], 'en', 1200, 'egyptian', false);

        $this->assertStringContainsString('KEYWORD DISCOVERY IS ENABLED', $enabled);
        $this->assertStringContainsString('KEYWORD DISCOVERY IS DISABLED', $disabled);
        $this->assertStringContainsString("empty 'lsi_keywords' array", $disabled);
    }

    public function test_visible_newline_escape_artifacts_are_removed_recursively(): void
    {
        $article = ArticleContentService::sanitizeGeneratedArticle([
            'title_ar' => 'عنوان\\nالمقال',
            'body_ar' => '<p>السطر الأول\\\\nالسطر الثاني</p>',
            'faq' => [['answer_ar' => 'إجابة\\r\\nواضحة']],
        ]);

        $this->assertStringNotContainsString('\\n', $article['title_ar']);
        $this->assertStringNotContainsString('\\n', $article['body_ar']);
        $this->assertStringNotContainsString('\\r', $article['faq'][0]['answer_ar']);
    }

    public function test_exactly_five_faq_items_are_rendered_into_the_article_body_once(): void
    {
        $faq = [];
        for ($index = 1; $index <= 5; $index++) {
            $faq[] = ['question_ar' => "السؤال {$index}", 'answer_ar' => "الإجابة {$index}"];
        }
        $article = [
            'body_ar' => '<h2>المحتوى</h2><p>تفاصيل المقال</p><h2>الخاتمة</h2><p>نهاية المقال</p>',
            'faq' => $faq,
        ];

        $rendered = ArticleContentService::renderFaqIntoBodies($article, 'ar');
        $renderedAgain = ArticleContentService::renderFaqIntoBodies($rendered, 'ar');

        $this->assertSame(1, substr_count($renderedAgain['body_ar'], 'class="article-faq"'));
        $this->assertSame(5, substr_count($renderedAgain['body_ar'], 'class="article-faq-item"'));
        $this->assertStringContainsString('الأسئلة الشائعة', $renderedAgain['body_ar']);
        $this->assertLessThan(
            strpos($renderedAgain['body_ar'], '<h2>الخاتمة</h2>'),
            strpos($renderedAgain['body_ar'], 'class="article-faq"')
        );
        $this->assertSame('FAQPage', $renderedAgain['faq_schema']['ar']['@type']);
        $this->assertCount(5, $renderedAgain['faq_schema']['ar']['mainEntity']);
    }

    public function test_stock_phrases_are_reported_for_arabic_and_english(): void
    {
        $base = [
            'slug' => 'original-seo-guide',
            'image' => ['alt' => ''],
        ];

        $arabic = array_merge($base, [
            'title_ar' => 'دليل تحسين محركات البحث للمواقع',
            'seo_title_ar' => 'تحسين محركات البحث للمواقع دليل عملي متكامل',
            'meta_description_ar' => 'دليل عملي لتحسين محركات البحث للمواقع مع خطوات واضحة تساعدك على بناء حضور أفضل وزيادة الزيارات بطريقة مفيدة.',
            'introduction_ar' => 'في عالم اليوم يحتاج صاحب الموقع إلى خطة واضحة لفهم نية الزائر وتحسين الصفحات بناءً على احتياجاته الفعلية.',
            'body_ar' => '<h2>خطة عملية</h2><p>ابدأ بتحليل الصفحات ثم رتب الأولويات ونفذ التحسينات القابلة للقياس خطوة بعد أخرى.</p>',
        ]);

        $english = array_merge($base, [
            'title_en' => 'A Practical SEO Guide for Modern Websites',
            'seo_title_en' => 'A Practical SEO Guide for Modern Websites',
            'meta_description_en' => 'Use this practical SEO guide to improve website structure, serve search intent, and create a clearer path for qualified visitors.',
            'introduction_en' => "In today's digital world, a website needs a clear plan for understanding visitor intent and improving pages around real needs.",
            'body_en' => '<h2>A practical plan</h2><p>Start by reviewing important pages, rank the work by impact, and measure each improvement after it is released.</p>',
        ]);

        $arReport = ArticleContentService::validateAndGenerateReport($arabic, 'تحسين محركات البحث للمواقع', 'ar');
        $enReport = ArticleContentService::validateAndGenerateReport($english, 'website SEO', 'en');

        $this->assertTrue($this->containsWarning($arReport['warnings'], 'Generic stock phrase'));
        $this->assertTrue($this->containsWarning($enReport['warnings'], 'Generic stock phrase'));
    }

    private function containsWarning(array $warnings, string $needle): bool
    {
        foreach ($warnings as $warning) {
            if (str_contains($warning, $needle)) {
                return true;
            }
        }

        return false;
    }
}
