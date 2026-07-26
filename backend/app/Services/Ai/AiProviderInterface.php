<?php

namespace App\Services\Ai;

interface AiProviderInterface
{
    /**
     * Generates structured content from a prompt.
     *
     * @param string $prompt
     * @param string $model
     * @param float $temperature
     * @param int $maxLength
     * @param string $token
     * @return array The structured JSON response decoded as an array.
     */
    public function generateStructuredContent(
        string $prompt,
        string $model,
        float $temperature,
        int $maxLength,
        string $token
    ): array;

    /**
     * Fetches the models this API key can actually use, live from the provider.
     *
     * @param string $token
     * @return array<string, string> Model id => human readable label.
     * @throws \Exception When the provider cannot be reached or rejects the token.
     */
    public function listModels(string $token): array;

    /**
     * Validates connection with the provider using the token.
     *
     * @param string $token
     * @return bool
     */
    public function testConnection(string $token): bool;
}
