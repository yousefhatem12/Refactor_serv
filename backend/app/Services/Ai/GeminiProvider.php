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
        $encodedModel = rawurlencode($model);
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$encodedModel}:generateContent";

        $response = Http::withHeaders([
            'x-goog-api-key' => $token,
        ])
            ->timeout(90)
            ->retry(1, 1500)
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

    public function listModels(string $token): array
    {
        $response = Http::withHeaders([
            'x-goog-api-key' => $token,
        ])->timeout(15)->get('https://generativelanguage.googleapis.com/v1beta/models', [
            'pageSize' => 200,
        ]);

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 500);
            throw new \Exception("Gemini model listing failed: {$response->status()} - {$excerpt}");
        }

        $models = [];
        foreach ($response->json('models') ?? [] as $model) {
            $name = $model['name'] ?? null;
            if (!$name) {
                continue;
            }

            // Only generative chat models can serve generateContent.
            if (!in_array('generateContent', $model['supportedGenerationMethods'] ?? [], true)) {
                continue;
            }

            $id = str_starts_with($name, 'models/') ? substr($name, 7) : $name;
            $models[$id] = $model['displayName'] ?? $id;
        }

        krsort($models);

        return $models;
    }

    public function testConnection(string $token): bool
    {
        try {
            // Listing models validates the key without burning generation quota
            // and without depending on a model name that may have been retired.
            return Http::timeout(10)
                ->get('https://generativelanguage.googleapis.com/v1beta/models', ['key' => $token])
                ->successful();
        } catch (\Throwable $e) {
            Log::warning("Gemini Connection test error: " . $e->getMessage());
            return false;
        }
    }
}
