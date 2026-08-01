<?php

namespace Tests\Unit;

use App\Services\Ai\ArticleContentService;
use PHPUnit\Framework\TestCase;

class ArticleContentServiceOriginalityTest extends TestCase
{
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
