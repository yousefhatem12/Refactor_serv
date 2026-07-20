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
     * Lists models supported by this provider.
     *
     * @return array
     */
    public function listSupportedModels(): array;

    /**
     * Validates connection with the provider using the token.
     *
     * @param string $token
     * @return bool
     */
    public function testConnection(string $token): bool;
}
