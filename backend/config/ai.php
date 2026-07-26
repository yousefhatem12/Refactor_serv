<?php

return [
    'enabled' => env('AI_CONTENT_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | AI Model Fallback Registry
    |--------------------------------------------------------------------------
    |
    | Model lists are normally fetched live from each provider with the stored
    | API key (see App\Services\Ai\ModelCatalog). This registry is only used
    | when no key is available yet or the provider listing call fails, so the
    | forms still render something usable.
    |
    */
    'models' => [
        'gemini' => [
            'gemini-2.0-flash' => 'Gemini 2.0 Flash',
            'gemini-1.5-flash' => 'Gemini 1.5 Flash',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
        ],
        'openai' => [
            'gpt-4o-mini' => 'GPT-4o Mini',
            'gpt-4o' => 'GPT-4o',
        ],
        'deepseek' => [
            'deepseek-v4-flash' => 'DeepSeek V4 Flash',
            'deepseek-v4-pro' => 'DeepSeek V4 Pro',
        ],
    ]
];
