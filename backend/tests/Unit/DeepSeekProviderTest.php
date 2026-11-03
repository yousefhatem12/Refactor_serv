<?php

namespace Tests\Unit;

use App\Services\Ai\DeepSeekProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeepSeekProviderTest extends TestCase
{
    public function test_deepseek_budgets_cover_cluster_single_and_bilingual_generation(): void
    {
        $this->assertSame(4096, DeepSeekProvider::CLUSTER_PLANNING_TOKENS);
        $this->assertSame(8192, DeepSeekProvider::SINGLE_LANGUAGE_ARTICLE_TOKENS);
        $this->assertSame(16384, DeepSeekProvider::BILINGUAL_ARTICLE_TOKENS);
    }

    public function test_structured_generation_disables_thinking_and_preserves_the_requested_output_budget(): void
    {
        Http::fake([
            'api.deepseek.com/chat/completions' => Http::response([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"ok":true}'],
                ]],
                'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 5, 'total_tokens' => 15],
            ]),
        ]);

        $result = (new DeepSeekProvider())->generateStructuredContent(
            'Return one JSON object.',
            'deepseek-v4-pro',
            0.7,
            6144,
            'test-token'
        );

        $this->assertTrue($result['ok']);
        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://api.deepseek.com/chat/completions'
                && $request['thinking'] === ['type' => 'disabled']
                && $request['max_tokens'] === 6144
                && $request['response_format'] === ['type' => 'json_object'];
        });
    }

    public function test_cluster_planning_keeps_its_original_compact_output_budget(): void
    {
        Http::fake([
            'api.deepseek.com/chat/completions' => Http::response([
                'choices' => [[
                    'finish_reason' => 'stop',
                    'message' => ['content' => '{"subtopics":[]}'],
                ]],
                'usage' => [],
            ]),
        ]);

        (new DeepSeekProvider())->generateStructuredContent(
            'Return a JSON cluster plan.',
            'deepseek-v4-pro',
            0.2,
            1200,
            'test-token'
        );

        Http::assertSent(fn ($request): bool => $request['max_tokens'] === 1200);
    }

    public function test_empty_completion_reports_the_model_and_finish_reason(): void
    {
        Http::fake([
            'api.deepseek.com/chat/completions' => Http::response([
                'choices' => [[
                    'finish_reason' => 'insufficient_system_resource',
                    'message' => ['content' => null, 'reasoning_content' => 'partial reasoning'],
                ]],
                'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 50, 'total_tokens' => 150],
            ]),
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'DeepSeek API returned empty completions response (model: deepseek-v4-pro, finish_reason: insufficient_system_resource).'
        );

        (new DeepSeekProvider())->generateStructuredContent(
            'Return one JSON object.',
            'deepseek-v4-pro',
            0.2,
            1200,
            'test-token'
        );
    }
}
