<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiProvider implements AiProviderInterface
{
    public function generateStructuredContent(
        string $prompt,
        string $model,
        float $temperature,
        int $maxLength,
        string $token
    ): array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$token}";

        $response = Http::timeout(120)
            ->retry(3, 2000)
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxLength,
                ]
            ]);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 1000);
            throw new \Exception("Gemini API call failed: {$response->status()} - {$excerpt}");
        }

        $data = $response->json();
        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$content) {
            throw new \Exception("Gemini API returned empty candidate response.");
        }

        try {
            $decoded = ArticleContentService::cleanAndDecodeJson($content);
        } catch (\Throwable $e) {
            throw new \Exception("Gemini response is not valid JSON: " . $e->getMessage());
        }

        // Mock token count usage for telemetry if not provided by Gemini
        $decoded['usage_metadata'] = [
            'prompt_tokens' => $data['usageMetadata']['promptTokenCount'] ?? 0,
            'completion_tokens' => $data['usageMetadata']['candidatesTokenCount'] ?? 0,
            'total_tokens' => $data['usageMetadata']['totalTokenCount'] ?? 0,
        ];

        return $decoded;
    }

    public function listSupportedModels(): array
    {
        return [
            'gemini-1.5-flash' => 'Gemini 1.5 Flash (Default)',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash (Experimental)',
        ];
    }

    public function testConnection(string $token): bool
    {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$token}";
            $response = Http::timeout(10)->post($url, [
                'contents' => [
                    ['parts' => [['text' => 'Ping']]]
                ],
                'generationConfig' => [
                    'maxOutputTokens' => 5
                ]
            ]);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::warning("Gemini Connection test error: " . $e->getMessage());
            return false;
        }
    }
}
