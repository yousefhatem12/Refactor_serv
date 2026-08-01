<?php

namespace Tests\Unit;

use App\Services\Ai\TopicClusterPlanningService;
use PHPUnit\Framework\TestCase;

class TopicClusterPlanningServiceTest extends TestCase
{
    public function test_prompt_requires_the_exact_number_of_real_subtopics(): void
    {
        $prompt = TopicClusterPlanningService::buildPrompt(
            'تصميم متجر إلكتروني',
            ['الدفع الإلكتروني'],
            'ar',
            'egyptian',
            3,
            true
        );

        $this->assertStringContainsString('exactly 3', $prompt);
        $this->assertStringContainsString('never a placeholder', $prompt);
        $this->assertStringContainsString('discover closely related search phrases', $prompt);
    }

    public function test_plan_validation_rejects_placeholders_duplicates_and_wrong_counts(): void
    {
        $this->expectException(\RuntimeException::class);

        TopicClusterPlanningService::validatePlan([
            'subtopics' => [
                ['keyword' => 'Subtopic 1'],
                ['keyword' => 'اختيار بوابة الدفع المناسبة'],
                ['keyword' => 'اختيار بوابة الدفع المناسبة'],
            ],
        ], 'تصميم متجر إلكتروني', 3);
    }

    public function test_valid_plan_is_normalized_and_preserves_search_intent(): void
    {
        $plan = TopicClusterPlanningService::validatePlan([
            'subtopics' => [
                ['keyword' => 'اختيار بوابة الدفع المناسبة', 'search_intent' => 'commercial', 'angle' => 'مقارنة عملية'],
                ['keyword' => 'تقليل خطوات إتمام الشراء', 'search_intent' => 'transactional', 'angle' => 'تحسين التحويل'],
            ],
        ], 'تصميم متجر إلكتروني احترافي', 2);

        $this->assertCount(2, $plan);
        $this->assertSame('commercial', $plan[0]['search_intent']);
    }
}
