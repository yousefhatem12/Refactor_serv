<?php

namespace App\Services\Ai;

class AiProviderManager
{
    /**
     * Resolves the implementation class for the given provider name.
     *
     * @param string $providerName
     * @return AiProviderInterface
     * @throws \InvalidArgumentException
     */
    public static function getProvider(string $providerName): AiProviderInterface
    {
        return match (strtolower(trim($providerName))) {
            'openai' => new OpenAiProvider(),
            'gemini' => new GeminiProvider(),
            'deepseek' => new DeepSeekProvider(),
            default => throw new \InvalidArgumentException("Unsupported AI provider: {$providerName}"),
        };
    }
}
