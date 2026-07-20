<?php

return [
    'enabled' => env('AI_CONTENT_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Centralized AI Model Registry
    |--------------------------------------------------------------------------
    |
    | Defines the curated list of supported models for each AI provider.
    |
    */
    'models' => [
        'gemini' => [
            'gemini-1.5-flash' => 'Gemini 1.5 Flash (Recommended)',
            'gemini-1.5-pro' => 'Gemini 1.5 Pro',
            'gemini-2.0-flash-exp' => 'Gemini 2.0 Flash (Experimental)',
        ],
        'openai' => [
            'gpt-4o-mini' => 'GPT-4o Mini (Recommended)',
            'gpt-4o' => 'GPT-4o',
            'gpt-4' => 'GPT-4',
        ],
        'deepseek' => [
            'deepseek-chat' => 'DeepSeek V3 (Chat - Recommended)',
            'deepseek-reasoner' => 'DeepSeek R1 (Reasoner)',
        ],
    ]
];
