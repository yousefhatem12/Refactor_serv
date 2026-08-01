<?php

namespace Tests\Unit;

use App\Services\Ai\SiteUrlBuilder;
use PHPUnit\Framework\TestCase;

class SiteUrlBuilderTest extends TestCase
{
    public function test_blog_urls_match_the_live_arabic_and_english_routes(): void
    {
        $this->assertSame('https://serv5.com.eg/ar/blog', SiteUrlBuilder::blogIndex('ar'));
        $this->assertSame('https://serv5.com.eg/en/blog', SiteUrlBuilder::blogIndex('en'));
        $this->assertSame('https://serv5.com.eg/ar/blog/test-post', SiteUrlBuilder::blogPost('ar', 'test-post'));
        $this->assertSame('https://serv5.com.eg/en/blog/test-post', SiteUrlBuilder::blogPost('en', 'test-post'));
    }

    public function test_service_urls_match_the_live_routes_without_double_prefixes(): void
    {
        $this->assertSame('https://serv5.com.eg/ar/services', SiteUrlBuilder::servicesIndex('ar'));
        $this->assertSame('https://serv5.com.eg/en/services', SiteUrlBuilder::servicesIndex('en'));
        $this->assertSame('https://serv5.com.eg/ar/services/services-web-hosting', SiteUrlBuilder::service('ar', 'web-hosting'));
        $this->assertSame('https://serv5.com.eg/en/services/services-web-hosting', SiteUrlBuilder::service('en', 'services-web-hosting'));
        $this->assertStringNotContainsString('services-services-', SiteUrlBuilder::service('ar', 'services-services-web-hosting'));
    }

    public function test_homepage_is_localized_and_keeps_its_root_slash(): void
    {
        $this->assertSame('https://serv5.com.eg/ar/', SiteUrlBuilder::home('ar'));
        $this->assertSame('https://serv5.com.eg/en/', SiteUrlBuilder::home('en'));
    }
}
