<?php

namespace Tests\Unit;

use App\Services\Ai\InternalLinkService;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class InternalLinkServiceTest extends TestCase
{
    private function callPrivate(string $method, mixed ...$arguments): mixed
    {
        $reflection = new ReflectionMethod(InternalLinkService::class, $method);
        $reflection->setAccessible(true);

        return $reflection->invoke(null, ...$arguments);
    }

    private function target(string $type, array $labels, string $arUrl, string $enUrl): array
    {
        return [
            'type' => $type,
            'labels' => $labels,
            'urls' => ['ar' => $arUrl, 'en' => $enUrl],
        ];
    }

    public function test_arabic_linking_uses_the_localized_url_without_scope_errors(): void
    {
        [$html, $used] = $this->callPrivate('linkText', 'تساعد خدماتنا في تحسين محركات البحث.', [
            $this->target(
                'service',
                ['ar' => ['خدماتنا', 'تحسين محركات البحث'], 'en' => ['Our Services', 'SEO Optimization']],
                'https://serv5.com.eg/ar/services/services-seo-optimization',
                'https://serv5.com.eg/en/services/services-seo-optimization',
            ),
        ], 'ar');

        $this->assertStringContainsString('https://serv5.com.eg/ar/services/services-seo-optimization', $html);
        $this->assertStringContainsString('/ar/', $used[0]['url']);
        $this->assertCount(1, $used);
    }

    public function test_english_linking_uses_the_english_url(): void
    {
        [$html, $used] = $this->callPrivate('linkText', 'Our Services include Web Design packages.', [
            $this->target(
                'service',
                ['ar' => ['خدماتنا'], 'en' => ['Our Services', 'Web Design']],
                'https://serv5.com.eg/ar/services/services-web-design',
                'https://serv5.com.eg/en/services/services-web-design',
            ),
        ], 'en');

        $this->assertStringContainsString('https://serv5.com.eg/en/services/services-web-design', $html);
        $this->assertStringContainsString('/en/', $used[0]['url']);
    }

    public function test_existing_anchor_is_never_wrapped_in_another_anchor(): void
    {
        $input = '<p><a href="https://serv5.com.eg/ar/services">خدماتنا</a> ثم خدماتنا</p>';
        [$html] = $this->callPrivate('linkText', $input, [
            $this->target('page', ['ar' => ['خدماتنا'], 'en' => ['Our Services']], 'https://serv5.com.eg/ar/services', 'https://serv5.com.eg/en/services'),
        ], 'ar');

        $this->assertStringNotContainsString('<a href="https://serv5.com.eg/ar/services"><a ', $html);
        $this->assertSame(2, substr_count($html, '<a href='));
    }

    public function test_linking_never_exceeds_four_new_links_per_article_field(): void
    {
        $input = '<p>خدماتنا</p><p>خدماتنا</p><p>خدماتنا</p><p>خدماتنا</p><p>خدماتنا</p>';
        [$html, $used] = $this->callPrivate('linkText', $input, [
            $this->target('page', ['ar' => ['خدماتنا'], 'en' => ['Our Services']], 'https://serv5.com.eg/ar/services', 'https://serv5.com.eg/en/services'),
        ], 'ar');

        $this->assertSame(4, substr_count($html, '<a href='));
        $this->assertCount(4, $used);
    }

    public function test_relevance_ranking_prefers_related_posts_and_services(): void
    {
        $targets = [
            $this->target('post', ['ar' => ['تحسين محركات البحث للمتاجر'], 'en' => ['SEO for Ecommerce']], 'https://serv5.com.eg/ar/blog/seo-ecommerce', 'https://serv5.com.eg/en/blog/seo-ecommerce'),
            $this->target('service', ['ar' => ['تصميم المواقع'], 'en' => ['Web Design']], 'https://serv5.com.eg/ar/services/services-web-design', 'https://serv5.com.eg/en/services/services-web-design'),
            $this->target('post', ['ar' => ['الحسابات البنكية'], 'en' => ['Bank Accounts']], 'https://serv5.com.eg/ar/bank-accounts', 'https://serv5.com.eg/en/bank-accounts'),
        ];

        $ranked = $this->callPrivate(
            'rankRelevantTargets',
            $targets,
            'ar',
            'أهمية السيو للمتاجر الإلكترونية',
            'يساعد تحسين محركات البحث في زيادة ظهور المتاجر الإلكترونية وتحسين نتائج البحث.'
        );

        $this->assertNotEmpty($ranked);
        $this->assertSame('https://serv5.com.eg/ar/blog/seo-ecommerce', $ranked[0]['urls']['ar']);
        $this->assertNotSame('https://serv5.com.eg/ar/bank-accounts', $ranked[0]['urls']['ar']);
    }

    public function test_relevance_ranking_uses_service_and_post_metadata_not_only_titles(): void
    {
        $related = $this->target('service', ['ar' => ['حلول الأعمال'], 'en' => ['Business Solutions']], 'https://serv5.com.eg/ar/services/database', 'https://serv5.com.eg/en/services/database');
        $related['search_text'] = ['ar' => 'استضافة قواعد بيانات PostgreSQL وإدارة النسخ الاحتياطي', 'en' => 'PostgreSQL database hosting and backups'];
        $unrelated = $this->target('service', ['ar' => ['تصميم الشعارات'], 'en' => ['Logo Design']], 'https://serv5.com.eg/ar/services/logo', 'https://serv5.com.eg/en/services/logo');
        $unrelated['search_text'] = ['ar' => 'هوية بصرية وألوان العلامة التجارية', 'en' => 'Brand identity and colors'];

        $ranked = $this->callPrivate('rankRelevantTargets', [$unrelated, $related], 'ar', 'استضافة PostgreSQL', 'اختيار قاعدة بيانات وإدارة النسخ الاحتياطي');

        $this->assertSame('https://serv5.com.eg/ar/services/database', $ranked[0]['urls']['ar']);
    }

    public function test_fuzzed_html_does_not_throw_or_create_more_than_four_new_links(): void
    {
        $targets = [
            $this->target('page', ['ar' => ['خدماتنا'], 'en' => ['Our Services']], 'https://serv5.com.eg/ar/services', 'https://serv5.com.eg/en/services'),
            $this->target('service', ['ar' => ['تحسين محركات البحث'], 'en' => ['SEO Optimization']], 'https://serv5.com.eg/ar/services/services-seo-optimization', 'https://serv5.com.eg/en/services/services-seo-optimization'),
        ];

        $fragments = [
            'خدماتنا',
            'تحسين محركات البحث',
            'نص عادي لا يحتوي على رابط',
            '<strong>خدماتنا</strong>',
            '<a href="https://serv5.com.eg/ar/contact-us">خدماتنا</a>',
            'SEO Optimization',
            '🎯',
        ];

        mt_srand(20260721);
        for ($run = 0; $run < 250; $run++) {
            $parts = [];
            for ($index = 0; $index < 20; $index++) {
                $parts[] = $fragments[mt_rand(0, count($fragments) - 1)];
            }

            $input = implode(' ', $parts);
            [$html, $used] = $this->callPrivate('linkText', $input, $targets, $run % 2 === 0 ? 'ar' : 'en');

            $this->assertLessThanOrEqual(4, count($used));
            $this->assertLessThanOrEqual(substr_count($input, '<a href=') + 4, substr_count($html, '<a href='));
            $this->assertStringNotContainsString('<a href="https://serv5.com.eg/ar/contact-us"><a ', $html);
        }
    }
}
