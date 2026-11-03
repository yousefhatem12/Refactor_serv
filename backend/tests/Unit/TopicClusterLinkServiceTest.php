<?php

namespace Tests\Unit;

use App\Models\AiGenerationItem;
use App\Models\Post;
use App\Services\Ai\ArticleContentService;
use App\Services\Ai\TopicClusterLinkService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class TopicClusterLinkServiceTest extends TestCase
{
    public function test_supporting_article_links_to_real_pillar_and_keeps_faq_before_conclusion(): void
    {
        $pillar = $this->item(1, 'دليل الاستضافة', 'hosting-pillar');
        $support = $this->item(2, 'اختيار خطة الاستضافة', 'hosting-plan');
        $sibling = $this->item(3, 'حماية الموقع', 'website-security');
        $article = ArticleContentService::renderFaqIntoBodies([
            'body_ar' => '<h2>التفاصيل</h2><p>محتوى المقال</p><h2>الخاتمة</h2><p>نهاية المقال</p>',
            'faq' => array_map(fn (int $number): array => [
                'question_ar' => "سؤال {$number}",
                'answer_ar' => "إجابة {$number}",
            ], range(1, 5)),
            'internal_link_suggestions' => [],
        ], 'ar');

        $linked = TopicClusterLinkService::apply(
            $article,
            $support,
            new Collection([$pillar, $support, $sibling]),
            'ar',
            'دليل الاستضافة'
        );

        $this->assertStringContainsString('https://serv5.com.eg/ar/blog/hosting-pillar', $linked['body_ar']);
        $this->assertStringContainsString('https://serv5.com.eg/ar/blog/website-security', $linked['body_ar']);
        $this->assertStringNotContainsString('class="topic-cluster-links"', $linked['body_ar']);
        preg_match_all('/href="([^"]+)"/', $linked['body_ar'], $linkedUrls);
        $this->assertSame(count($linkedUrls[1]), count(array_unique($linkedUrls[1])));
        $this->assertLessThan(strpos($linked['body_ar'], 'class="article-faq"'), strpos($linked['body_ar'], 'hosting-pillar'));
        $this->assertLessThan(strpos($linked['body_ar'], 'class="article-faq"'), strpos($linked['body_ar'], 'website-security'));

        $linkedAgain = TopicClusterLinkService::apply(
            $linked,
            $support,
            new Collection([$pillar, $support, $sibling]),
            'ar',
            'دليل الاستضافة'
        );
        preg_match_all('/href="([^"]+)"/', $linkedAgain['body_ar'], $linkedAgainUrls);
        $this->assertSame(count($linkedAgainUrls[1]), count(array_unique($linkedAgainUrls[1])));
        $this->assertSame(1, substr_count($linkedAgain['body_ar'], 'hosting-pillar'));
        $this->assertSame(1, substr_count($linkedAgain['body_ar'], 'website-security'));
    }

    private function item(int $id, string $title, string $slug): AiGenerationItem
    {
        $post = new Post(['title' => ['ar' => $title, 'en' => null], 'slug' => $slug]);
        $post->id = $id;
        $item = new AiGenerationItem(['keyword' => $title]);
        $item->id = $id;
        $item->setRelation('post', $post);
        return $item;
    }
}
