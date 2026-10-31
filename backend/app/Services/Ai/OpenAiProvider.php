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
        ->timeout(90)
        ->retry(1, 1500)
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

    /**
     * Model ids that answer /v1/models but cannot serve chat completions.
     */
    private const NON_CHAT_PATTERNS = [
        'embedding', 'whisper', 'tts', 'dall-e', 'moderation', 'audio',
        'image', 'realtime', 'transcribe', 'search', 'similarity', 'edit',
        'instruct', 'davinci', 'babbage', 'curie', 'ada', 'codex',
    ];

    public function listModels(string $token): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$token}",
        ])
        ->timeout(15)
        ->get('https://api.openai.com/v1/models');

        if ($response->failed()) {
            $excerpt = substr($response->body(), 0, 500);
            throw new \Exception("OpenAI model listing failed: {$response->status()} - {$excerpt}");
        }

        $models = [];
        foreach ($response->json('data') ?? [] as $model) {
            $id = $model['id'] ?? null;
            if (!$id || !self::isChatModel($id)) {
                continue;
            }
            $models[$id] = $id;
        }

        krsort($models);

        return $models;
    }

    private static function isChatModel(string $id): bool
    {
        if (!preg_match('/^(gpt-|o\d|chatgpt-)/i', $id)) {
            return false;
        }

        foreach (self::NON_CHAT_PATTERNS as $pattern) {
            if (str_contains($id, $pattern)) {
                return false;
            }
        }

        return true;
    }

    public function testConnection(string $token): bool
    {
        try {
            // Listing models validates the key without burning completion tokens
            // and without depending on a model name that may have been retired.
            return Http::withHeaders([
                'Authorization' => "Bearer {$token}",
            ])
            ->timeout(10)
            ->get('https://api.openai.com/v1/models')
            ->successful();
        } catch (\Throwable $e) {
            Log::warning("OpenAI Connection test error: " . $e->getMessage());
            return false;
        }
    }
}
