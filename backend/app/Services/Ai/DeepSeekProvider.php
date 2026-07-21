<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DeepSeekProvider implements AiProviderInterface
{
    public function generateStructuredContent(
        string $prompt,
        string $model,
        float $temperature,
        int $maxLength,
        string $token
    ): array {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
        // Queue-level retries handle real failures. Retrying a 120-second AI
        // request three times here can leave a single job running for minutes.
        ->timeout(150)
        ->retry(1, 1000)
        ->post('https://api.deepseek.com/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => 'You are a precise JSON API. Return exactly one valid UTF-8 JSON object matching the requested schema. Never emit Markdown fences, raw control characters, or explanatory text.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxLength,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 1000);
            throw new \Exception("DeepSeek API call failed: {$response->status()} - {$excerpt}");
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            throw new \Exception("DeepSeek API returned empty completions response.");
        }

        try {
            $decoded = ArticleContentService::cleanAndDecodeJson($content);
        } catch (\Throwable $e) {
            throw new \Exception("DeepSeek response is not valid JSON: " . $e->getMessage());
        }

        $decoded['usage_metadata'] = [
            'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
            'completion_tokens' => $data['usage']['completion_tokens'] ?? 0,
            'total_tokens' => $data['usage']['total_tokens'] ?? 0,
        ];

        return $decoded;
    }

    public function listSupportedModels(): array
    {
        return [
            'deepseek-chat' => 'DeepSeek V3 (Chat)',
            'deepseek-reasoner' => 'DeepSeek R1 (Reasoner)',
        ];
    }

    public function testConnection(string $token): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->timeout(10)
            ->post('https://api.deepseek.com/chat/completions', [
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => 'Ping']
                ],
                'max_tokens' => 5,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("DeepSeek Connection test error: " . $e->getMessage());
            return false;
        }
    }
}
