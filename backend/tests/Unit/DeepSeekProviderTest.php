<?php

namespace Tests\Unit;

use App\Services\Ai\DeepSeekProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeepSeekProviderTest extends TestCase
{
    public function test_structured_generation_keeps_thinking_and_expands_the_output_budget(): void
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
                && $request['thinking'] === ['type' => 'enabled']
                && $request['max_tokens'] === 24576
                && $request['response_format'] === ['type' => 'json_object'];
        });
    }

    public function test_cluster_planning_gets_a_safe_thinking_budget(): void
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

        Http::assertSent(fn ($request): bool => $request['max_tokens'] === 8192);
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
