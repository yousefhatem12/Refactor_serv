<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiProvider implements AiProviderInterface
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
        ->timeout(120)
        ->retry(3, 2000)
        ->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxLength,
            'response_format' => ['type' => 'json_object'],
        ]);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 1000);
            throw new \Exception("OpenAI API call failed: {$response->status()} - {$excerpt}");
        }

        $data = $response->json();
        $content = $data['choices'][0]['message']['content'] ?? null;

        if (!$content) {
            throw new \Exception("OpenAI API returned an empty completion content.");
        }

        try {
            $decoded = ArticleContentService::cleanAndDecodeJson($content);
        } catch (\Throwable $e) {
            throw new \Exception("OpenAI response content is not valid JSON: " . $e->getMessage());
        }

        // Inject usage metadata
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
            'gpt-4o' => 'GPT-4o (Default)',
            'gpt-4o-mini' => 'GPT-4o-mini',
            'gpt-4' => 'GPT-4 (Legacy)',
        ];
    }

    public function testConnection(string $token): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->timeout(10)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => 'Ping']
                ],
                'max_tokens' => 5,
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("OpenAI Connection test error: " . $e->getMessage());
            return false;
        }
    }
}
