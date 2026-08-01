<?php

namespace Tests\Unit;

use App\Services\Ai\GeneratedHtmlSanitizer;
use PHPUnit\Framework\TestCase;

class GeneratedHtmlSanitizerTest extends TestCase
{
    public function test_it_removes_executable_html_and_unsafe_attributes(): void
    {
        $html = '<script>alert(1)</script><h2 onclick="evil()">عنوان</h2>'
            . '<p style="color:red"><img src=x onerror="evil()">نص</p>'
            . '<a href="javascript:alert(1)" class="internal-link evil">خطر</a>'
            . '<a href="https://serv5.com.eg/ar/services/services-web-hosting" class="internal-link evil" target="_blank">آمن</a>';

        $clean = GeneratedHtmlSanitizer::sanitize($html);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('<img', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('style=', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('target=', $clean);
        $this->assertStringContainsString('https://serv5.com.eg/ar/services/services-web-hosting', $clean);
        $this->assertStringContainsString('class="internal-link"', $clean);
    }

    public function test_it_preserves_the_safe_article_and_faq_structure(): void
    {
        $html = '<h2>عنوان</h2><p>فقرة <strong>مهمة</strong></p>'
            . '<section class="article-faq"><div class="article-faq-item"><h3>سؤال</h3><p>إجابة</p></div></section>';

        $clean = GeneratedHtmlSanitizer::sanitize($html);

        $this->assertStringContainsString('<h2>عنوان</h2>', $clean);
        $this->assertStringContainsString('class="article-faq"', $clean);
        $this->assertStringContainsString('class="article-faq-item"', $clean);
    }
}
